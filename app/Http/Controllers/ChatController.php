<?php

namespace App\Http\Controllers;

use App\Models\Sim;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'Silakan login sebagai Admin.');
        }

        $sims = Sim::all();
        $currentSim = $request->query('current_sim') ?? $request->session()->get('current_sim');
        $messages = [];
        $unreadCounts = [];

        Log::info('Chat index accessed', ['current_sim' => $currentSim, 'auth_id' => Auth::id()]);

        if ($currentSim) {
            $request->session()->put('current_sim', $currentSim);
            Log::info('Current SIM set in session', ['current_sim' => $currentSim]);
        }

        foreach ($sims as $sim) {
            $unreadCounts[$sim->nomor_sim] = Message::where('sender_id', $sim->nomor_sim)
                                                   ->where('sender_type', 'user')
                                                   ->where('receiver_id', Auth::id())
                                                   ->where('is_read', false)
                                                   ->count();
        }

        if ($currentSim) {
            $messages = Message::where(function ($query) use ($currentSim) {
                $query->where('sender_id', $currentSim)->where('sender_type', 'user')
                      ->orWhere(function ($q) use ($currentSim) {
                          $q->where('sender_type', 'admin')->where('receiver_id', $currentSim);
                      });
            })->orderBy('created_at', 'asc')->get();
            Log::info('Messages retrieved', ['count' => $messages->count(), 'current_sim' => $currentSim]);
        }

        return view('chat.index', compact('sims', 'messages', 'unreadCounts', 'currentSim'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'sim_number' => 'required|string',
        ]);

        $senderId = null;
        $senderType = null;
        $receiverId = null;

        Log::info('sendMessage called', [
            'auth_check' => Auth::check(),
            'sim_number' => $request->input('sim_number'),
            'session_public_sim' => $request->session()->get('public_sim'),
            'referer' => $request->header('referer')
        ]);

        if (Auth::check()) {
            $senderId = Auth::id();
            $senderType = 'admin';
            $receiverId = $request->input('sim_number');
            if (!Sim::where('nomor_sim', $receiverId)->exists()) {
                Log::error('Invalid SIM number', ['sim_number' => $receiverId]);
                return response()->json(['success' => false, 'message' => 'Nomor SIM tidak valid.'], 400);
            }
        } else {
            $sim = $request->session()->get('public_sim');
            if ($sim && isset($sim->nomor_sim) && $sim->nomor_sim === $request->input('sim_number')) {
                $senderId = $sim->nomor_sim;
                $senderType = 'user';
                $receiverId = 1;
            } else {
                Log::error('Invalid session for Pemegang SIM', [
                    'session_public_sim' => $sim,
                    'sim_number' => $request->input('sim_number')
                ]);
                return response()->json(['success' => false, 'message' => 'Sesi tidak valid. Silakan cari SIM terlebih dahulu.'], 401);
            }
        }

        $message = new Message();
        $message->message = $request->input('message');
        $message->sender_id = $senderId;
        $message->sender_type = $senderType;
        $message->receiver_id = $receiverId;
        $message->is_read = false;
        $message->save();

        Log::info('Message saved', [
            'sender_id' => $senderId,
            'sender_type' => $senderType,
            'receiver_id' => $receiverId,
            'message_id' => $message->id
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'message' => $message->message,
                'sender_type' => $message->sender_type,
                'created_at' => $message->created_at->format('Y-m-d H:i:s')
            ]
        ]);
    }

    public function getMessages(Request $request)
    {
        $simNumber = $request->query('sim_number');
        Log::info('getMessages called', ['sim_number' => $simNumber]);

        if (!$simNumber) {
            Log::error('No sim_number provided');
            return response()->json(['success' => false, 'message' => 'Nomor SIM tidak diberikan.'], 400);
        }

        $messages = Message::where(function ($query) use ($simNumber) {
            $query->where('sender_id', $simNumber)->where('sender_type', 'user')
                  ->orWhere(function ($q) use ($simNumber) {
                      $q->where('sender_type', 'admin')->where('receiver_id', $simNumber);
                  });
        })->orderBy('created_at', 'asc')->get()->map(function ($message) {
            $message->created_at = $message->created_at->format('Y-m-d H:i:s');
            return $message;
        });

        Log::info('Messages retrieved', ['count' => $messages->count(), 'sim_number' => $simNumber]);
        return response()->json($messages);
    }

    public function user(Request $request)
    {
        $sim = $request->session()->get('public_sim');
        if (!$sim || !isset($sim->nomor_sim)) {
            return redirect()->route('welcome')->with('error', 'Silakan cari SIM terlebih dahulu untuk mengakses chat.');
        }

        $simNumber = $sim->nomor_sim;
        $simData = Sim::where('nomor_sim', $simNumber)->first();
        if (!$simData) {
            $request->session()->forget('public_sim');
            return redirect()->route('welcome')->with('error', 'Nomor SIM tidak ditemukan. Silakan cari SIM terlebih dahulu.');
        }

        $messages = Message::where(function ($query) use ($simNumber) {
            $query->where('sender_id', $simNumber)->where('sender_type', 'user')
                  ->orWhere(function ($q) use ($simNumber) {
                      $q->where('sender_type', 'admin')->where('receiver_id', $simNumber);
                  });
        })->orderBy('created_at', 'asc')->get();

        Message::where('sender_type', 'admin')
               ->where('receiver_id', $simNumber)
               ->where('is_read', false)
               ->update(['is_read' => true]);

        Log::info('Chat user accessed', ['sim_number' => $simNumber]);
        return view('chat.user', compact('sim', 'messages'));
    }

    public function markAsRead($message_id)
    {
        $message = Message::findOrFail($message_id);
        $message->update(['is_read' => true]);
        Log::info('Message marked as read', ['message_id' => $message_id]);
        return redirect()->back();
    }
}
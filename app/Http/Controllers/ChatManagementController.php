<?php

namespace App\Http\Controllers;

use App\Models\Sim;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatManagementController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'Admin') {
            return redirect()->route('admin.login')->with('error', 'Silakan login sebagai Admin.');
        }

        $sims = Sim::all();
        $currentSim = $request->query('current_sim') ?? session('current_sim');
        $messages = $currentSim ? Message::where(function ($query) use ($currentSim) {
            $query->where('sender_id', $currentSim)->where('sender_type', 'user')
                  ->orWhere(function ($q) use ($currentSim) {
                      $q->where('sender_type', 'admin')->where('receiver_id', $currentSim);
                  });
        })->orderBy('created_at', 'asc')->get() : [];

        if ($currentSim) {
            session(['current_sim' => $currentSim]);
        }

        $unreadCounts = [];
        foreach ($sims as $sim) {
            $unreadCounts[$sim->nomor_sim] = Message::where('sender_id', $sim->nomor_sim)
                                                   ->where('sender_type', 'user')
                                                   ->where('receiver_id', Auth::id())
                                                   ->where('is_read', false)
                                                   ->count();
        }

        return view('chat.index', compact('sims', 'messages', 'unreadCounts', 'currentSim'));
    }

    public function user(Request $request)
    {
        $sim = session('public_sim');
        if (!$sim || !isset($sim->nomor_sim)) {
            return redirect()->route('welcome')->with('error', 'Silakan cari SIM terlebih dahulu.');
        }

        $simNumber = $sim->nomor_sim;
        $messages = Message::where(function ($query) use ($simNumber) {
            $query->where('sender_id', $simNumber)->where('sender_type', 'user')
                  ->orWhere(function ($q) use ($simNumber) {
                      $q->where('sender_type', 'admin')->where('receiver_id', $simNumber);
                  });
        })->orderBy('created_at', 'asc')->get();

        // Tandai pesan dari admin sebagai dibaca
        Message::where('sender_type', 'admin')
               ->where('receiver_id', $simNumber)
               ->where('is_read', false)
               ->update(['is_read' => true]);

        return view('chat.user', compact('sim', 'messages'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'sim_number' => 'required|string',
        ]);

        $senderId = Auth::check() ? Auth::id() : session('public_sim')->nomor_sim;
        $senderType = Auth::check() ? 'admin' : 'user';
        $receiverId = Auth::check() ? $request->input('sim_number') : 1; // ID admin default

        if (Auth::check() && !Sim::where('nomor_sim', $receiverId)->exists()) {
            return response()->json(['success' => false, 'message' => 'Nomor SIM tidak valid.'], 400);
        }

        $message = Message::create([
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $request->input('message'),
            'is_read' => false,
        ]);

        // Broadcast pesan menggunakan Pusher
        broadcast(new \App\Events\MessageSent($message))->toOthers();

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function getMessages(Request $request)
    {
        $simNumber = $request->query('sim_number');
        if (!$simNumber) {
            return response()->json(['success' => false, 'message' => 'Nomor SIM tidak diberikan.'], 400);
        }

        $messages = Message::where(function ($query) use ($simNumber) {
            $query->where('sender_id', $simNumber)->where('sender_type', 'user')
                  ->orWhere(function ($q) use ($simNumber) {
                      $q->where('sender_type', 'admin')->where('receiver_id', $simNumber);
                  });
        })->orderBy('created_at', 'asc')->get();

        if (Auth::check()) {
            Message::where('sender_type', 'user')
                   ->where('receiver_id', Auth::id())
                   ->where('sender_id', $simNumber)
                   ->where('is_read', false)
                   ->update(['is_read' => true]);
        }

        return response()->json($messages);
    }
}
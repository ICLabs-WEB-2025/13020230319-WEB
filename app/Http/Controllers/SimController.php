<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;

class SimController extends Controller
{
    public function welcome(Request $request)
    {
        $sim = $request->session()->get('public_sim');
        $simNumber = $sim ? $sim->nomor_sim : null;
        $messages = $simNumber ? Message::where('sender_id', $simNumber)
                                ->orWhere(function ($query) use ($simNumber) {
                                    $query->where('sender_type', 'admin')
                                          ->where('receiver_id', $simNumber);
                                })
                                ->orderBy('created_at', 'asc')
                                ->get() : collect([]);
        return view('welcome', compact('sim', 'messages'));
    }

    public function publicSearch(Request $request)
    {
        $request->validate([
            'nomor_sim' => 'required|string',
            'tanggal_lahir' => 'required|date',
        ]);

        $sim = Sim::where('nomor_sim', $request->nomor_sim)
                  ->where('tanggal_lahir', $request->tanggal_lahir)
                  ->first();

        if ($sim) {
            session(['public_sim' => $sim]);
            return view('welcome', compact('sim'));
        }

        return redirect()->route('welcome')->with('error', 'Nomor SIM atau tanggal lahir tidak cocok.');
    }
    public function dashboard(Request $request)
    {
        $sims = Sim::query();
        $filterType = $request->input('filter_type', 'penerbitan');
        $filter = $request->input('filter');
        $query = $request->input('query');

        if ($query) {
            $sims->where(function ($q) use ($query) {
                $q->where('nama', 'like', "%$query%")
                  ->orWhere('nomor_sim', 'like', "%$query%")
                  ->orWhere('jenis_sim', 'like', "%$query%");
            });
        }

        if ($filter) {
            $oneYearAgo = now()->subYear();
            if ($filterType === 'penerbitan') {
                if ($filter === 'baru') {
                    $sims->where('tanggal_penerbitan', '>=', $oneYearAgo);
                } elseif ($filter === 'lama') {
                    $sims->where('tanggal_penerbitan', '<', $oneYearAgo);
                }
            } elseif ($filterType === 'berlaku') {
                if ($filter === 'baru') {
                    $sims->where('masa_berlaku', '>=', now());
                } elseif ($filter === 'lama') {
                    $sims->where('masa_berlaku', '<', now());
                }
            }
        }

        $sims = $sims->paginate(10);
        $users = User::all();
        return view('dashboard', compact('sims', 'users', 'filterType', 'filter', 'query'));
    }

    public function createSimForm()
    {
        return view('sim.create');
    }

    public function storeSim(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required',
            'pekerjaan' => 'nullable',
            'jenis_sim' => 'required',
            'nomor_ktp' => 'required|unique:sims,nomor_ktp',
        ]);

        do {
            $randomNum = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $nomor_sim = 'SIM-' . now()->format('Y') . '-' . $randomNum;
        } while (Sim::where('nomor_sim', $nomor_sim)->exists());

        Sim::create([
            'user_id' => Auth::id(),
            'nomor_sim' => $nomor_sim,
            'nama' => $request->nama,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'pekerjaan' => $request->pekerjaan,
            'jenis_sim' => $request->jenis_sim,
            'nomor_ktp' => $request->nomor_ktp,
            'tanggal_penerbitan' => now(),
            'masa_berlaku' => now()->addYears(5),
            'status' => 'Aktif',
            'created_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'SIM berhasil ditambahkan dengan nomor ' . $nomor_sim);
    }

    public function editSimForm($sim_id)
    {
        $sim = Sim::findOrFail($sim_id);
        return view('sim.edit', compact('sim'));
    }

    public function updateSim(Request $request, $sim_id)
    {
        $sim = Sim::findOrFail($sim_id);
        $request->validate([
            'nama' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required',
            'pekerjaan' => 'nullable',
            'jenis_sim' => 'required',
            'nomor_ktp' => 'required|unique:sims,nomor_ktp,' . $sim->sim_id . ',sim_id',
        ]);

        $sim->update([
            'nama' => $request->nama,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'pekerjaan' => $request->pekerjaan,
            'jenis_sim' => $request->jenis_sim,
            'nomor_ktp' => $request->nomor_ktp,
        ]);

        return redirect()->route('dashboard')->with('success', 'SIM berhasil diperbarui');
    }

    public function deleteSim($sim_id)
    {
        $sim = Sim::findOrFail($sim_id);
        $sim->delete();
        return redirect()->route('dashboard')->with('success', 'SIM berhasil dihapus');
    }

    public function viewSim($sim_id)
    {
        $sim = Sim::findOrFail($sim_id);
        return view('sim.view', compact('sim'));
    }

    public function exportSimsToPDF()
    {
        $sims = Sim::all();

        $data = [
            'sims' => $sims,
            'title' => 'Laporan Data Pemegang SIM',
            'date' => now()->format('d-m-Y H:i:s'),
        ];

        $pdf = Pdf::loadView('exports.sims-pdf', $data);

        return $pdf->download('laporan-data-pemegang-sim-' . now()->format('YmdHis') . '.pdf');
    }

    public function exportSimsToCSV()
    {
        $sims = Sim::all();

        $headers = [
            'Nomor SIM', 'Nama', 'Tempat Lahir', 'Tanggal Lahir', 'Jenis Kelamin',
            'Alamat', 'Pekerjaan', 'Jenis SIM', 'Nomor KTP', 'Tanggal Penerbitan',
            'Masa Berlaku', 'Status', 'Created At'
        ];
        $callback = function () use ($sims, $headers) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // Tambahkan BOM untuk UTF-8
            fputcsv($file, $headers);

            foreach ($sims as $sim) {
                $row = [
                    $sim->nomor_sim,
                    $sim->nama,
                    $sim->tempat_lahir,
                    $sim->tanggal_lahir ? $sim->tanggal_lahir->format('d-m-Y') : '',
                    $sim->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
                    $sim->alamat,
                    $sim->pekerjaan ?? 'Tidak ada',
                    $sim->jenis_sim,
                    $sim->nomor_ktp,
                    $sim->tanggal_penerbitan ? $sim->tanggal_penerbitan->format('d-m-Y') : '',
                    $sim->masa_berlaku ? $sim->masa_berlaku->format('d-m-Y') : '',
                    $sim->status,
                    $sim->created_at,
                ];
                fputcsv($file, $row);
            }

            fclose($file);
        };

        $filename = 'laporan-data-pemegang-sim-' . now()->format('YmdHis') . '.csv';
        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
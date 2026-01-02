<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RelasiFktpApotek;
use Illuminate\Support\Facades\Log;

class RelasiFktpApotekController extends Controller
{
    public function search(Request $request)
    {
        $keyword = $request->term;

        try {
            $query = RelasiFktpApotek::query();

            $user = $request->user();
            if ($user && isset($user->role) && strtolower($user->role) === 'apotek') {
                if (!empty($user->kode_apotek)) {
                    $query->where(function ($q) use ($lkode) {
                        $q->where('kode_apotek', $lkode)
                           ->orWhereRaw("TRIM(LEADING '0' FROM kode_apotek) = TRIM(LEADING '0' FROM ?)", [$lkode]);
                    });
                } else {
                    return response()->json([]);
                }
            } else {
                $query->where(function ($q) use ($keyword) {
                    $q->where('nama_fktp', 'LIKE', "%{$keyword}%")
                      ->orWhere('kode_fktp', 'LIKE', "%{$keyword}%");
                });
            }
            $sql = $query->toSql();
            $bindings = $query->getBindings();

            $data = $query->limit(20)->get();

            $results = [];
            foreach ($data as $row) {
                $results[] = [
                    'id' => $row->kode_fktp,
                    'text' => $row->kode_fktp . ' - ' . $row->nama_fktp
                ];
            }

            $debugUser = null;
            if ($user) {
                $debugUser = [
                    'id' => $user->id_user ?? null,
                    'username' => $user->username ?? null,
                    'role' => $user->role ?? null,
                    'kode_apotek' => $user->kode_apotek ?? null,
                ];
            }

            Log::debug('RelasiFktpApotekController::search', [
                'user' => $debugUser,
                'sql' => $sql,
                'bindings' => $bindings,
                'result_count' => count($results),
            ]);

            if ($request->query('debug') == '1') {
                return response()->json([
                    'results' => $results,
                    'debug' => [
                        'user' => $debugUser,
                        'sql' => $sql,
                        'bindings' => $bindings,
                    ],
                ]);
            }

            return response()->json($results);
        } catch (\Exception $e) {
            Log::error('RelasiFktpApotekController@search error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

}

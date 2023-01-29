<?php

namespace App\Http\Controllers\Cargo;

use Illuminate\Http\Request;

use App\Models\Status;
use App\Models\Track;
use App\Models\TrackStatus;

class TrackExtensionController extends Controller
{
    public function insertTracks()
    {
        $statusArrived = Status::where('slug', 'arrived')
            ->orWhere('id', 5)
            ->select('id', 'slug')
            ->first();

        $fh = fopen('file-manager/tracks/tracks.txt', 'r');

        $trackCodes = [];

        while ($line = fgets($fh)) {
            $trackCodes[] = trim($line);
        }

        // Update Unarrived Tracks
        $unarrivedTracks = Track::whereIn('code', $trackCodes)->where('status', '!=', '5')->get();
        $unarrivedTracksCode = $unarrivedTracks->pluck('code');
        $unarrivedTracksStatus = [];

        $unarrivedTracks->each(function ($item, $key) use (&$unarrivedTracksStatus, $statusArrived) {
            $unarrivedTracksStatus[] = [
                'track_id' => $item->id,
                'status_id' => $statusArrived->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        if ($unarrivedTracks->count() >= 1) {

            try {
                $resultInsert = TrackStatus::insert($unarrivedTracksStatus);

                $resultUpdate = Track::whereIn('id', $unarrivedTracks->pluck('id')->toArray())
                    ->update(['status' => $statusArrived->id]);

                if (!$resultInsert OR !$resultUpdate) {
                    throw new \Exception("Error Processing Request", 1);
                }
            } catch (\Exception $e) {
                echo 'Error: '.$e->getMessage();
            }
        }

        // Create Tracks
        $lang = app()->getLocale();
        $nonexistentTracksCode = collect($trackCodes)->diff($unarrivedTracksCode);

        foreach($nonexistentTracksCode as $code) {

            $newTrack = new Track;
            $newTrack->user_id = null;
            $newTrack->lang = $lang;
            $newTrack->code = $code;
            $newTrack->description = '';
            $newTrack->status  = $statusArrived->id;
            $newTrack->save();

            $trackStatus = new TrackStatus();
            $trackStatus->track_id = $newTrack->id;
            $trackStatus->status_id = $statusArrived->id;
            $trackStatus->created_at = now();
            $trackStatus->updated_at = now();
            $trackStatus->save();
        }

        fclose($fh);

        dd($unarrivedTracksCode, $unarrivedTracksStatus, $nonexistentTracksCode, $newTrack);
    }
}

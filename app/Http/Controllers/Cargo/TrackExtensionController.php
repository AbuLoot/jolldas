<?php

namespace App\Http\Controllers\Cargo;

use Illuminate\Http\Request;

use Rap2hpoutre\FastExcel\FastExcel;

use App\Models\Status;
use App\Models\Track;
use App\Models\TrackStatus;

class TrackExtensionController extends Controller
{
    public function uploadTracks(Request $request)
    {
        $this->validate($request, [
            'tracksDoc' => 'required|mimetypes:application/vnd.oasis.opendocument.spreadsheet,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel'
        ]);

        $docName = date('t-m-d H:i:s').'.'.$request->file('tracksDoc')->extension();

        dd($docName);

        $request->tracksDoc->storeAs('files', $docName, 'public');

        $tracksDoc = (new FastExcel)->import('/files/'.$docName, function($line) {
            dd($line);
            // return = [
                // 'code' => 
            // ];
        });
    }

    public function receptionTracks()
    {
        $statusReceived = Status::where('slug', 'received')
            ->orWhere('id', 2)
            ->select('id', 'slug')
            ->first();

        $fh = fopen('file-manager/tracks/reception-tracks.txt', 'r');

        $trackCodes = [];

        while ($line = fgets($fh)) {
            $trackCodes[] = trim($line);
        }

        fclose($fh);

        // Update Unreceived Tracks
        $unreceivedTracks = Track::whereIn('code', $trackCodes)->where('status', '!=', $statusReceived->id)->get();
        $unreceivedTracksCode = $unreceivedTracks->pluck('code');
        $unreceivedTracksStatus = [];

        $unreceivedTracks->each(function ($item, $key) use (&$unreceivedTracksStatus, $statusReceived) {
            $unreceivedTracksStatus[] = [
                'track_id' => $item->id,
                'status_id' => $statusReceived->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        if ($unreceivedTracks->count() >= 1) {

            try {
                $resultInsert = TrackStatus::insert($unreceivedTracksStatus);

                $resultUpdate = Track::whereIn('id', $unreceivedTracks->pluck('id')->toArray())
                    ->update(['status' => $statusReceived->id]);

                if (!$resultInsert OR !$resultUpdate) {
                    throw new \Exception("Error Processing Request", 1);
                }
            } catch (\Exception $e) {
                echo 'Error: '.$e->getMessage();
            }
        }

        // Create Tracks
        $lang = app()->getLocale();
        $nonexistentTracksCode = collect($trackCodes)->diff($unreceivedTracksCode);

        foreach($nonexistentTracksCode as $code) {

            $newTrack = new Track;
            $newTrack->user_id = null;
            $newTrack->lang = $lang;
            $newTrack->code = $code;
            $newTrack->description = '';
            $newTrack->status  = $statusReceived->id;
            $newTrack->save();

            $trackStatus = new TrackStatus();
            $trackStatus->track_id = $newTrack->id;
            $trackStatus->status_id = $statusReceived->id;
            $trackStatus->created_at = now();
            $trackStatus->updated_at = now();
            $trackStatus->save();
        }

        dd($unreceivedTracksCode, $unreceivedTracksStatus, $nonexistentTracksCode, $newTrack);
    }

    public function arrivalTracks()
    {
        $statusArrived = Status::where('slug', 'arrived')
            ->orWhere('id', 5)
            ->select('id', 'slug')
            ->first();

        $fh = fopen('file-manager/tracks/arrival-tracks.txt', 'r');

        $trackCodes = [];

        while ($line = fgets($fh)) {
            $trackCodes[] = trim($line);
        }

        fclose($fh);

        // Update Unarrived Tracks
        $unarrivedTracks = Track::whereIn('code', $trackCodes)->where('status', '!=', $statusArrived->id)->get();
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

        dd($unarrivedTracksCode, $unarrivedTracksStatus, $nonexistentTracksCode, $newTrack);
    }
}

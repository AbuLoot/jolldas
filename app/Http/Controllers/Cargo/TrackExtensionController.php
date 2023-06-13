<?php

namespace App\Http\Controllers\Cargo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Rap2hpoutre\FastExcel\FastExcel;

use App\Models\Status;
use App\Models\Track;
use App\Models\TrackStatus;

class TrackExtensionController extends Controller
{
    public $lang;

    public function __construct()
    {
        $this->lang = app()->getLocale();
    }

    public function uploadTracks(Request $request)
    {
        $this->validate($request, [
            'tracksDoc' => 'required|mimetypes:application/vnd.oasis.opendocument.spreadsheet,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel'
        ]);

        $docName = date('t-m-d H:i:s').'.'.$request->file('tracksDoc')->extension();

        $request->tracksDoc->storeAs('files', $docName);

        $trackCodes = (new FastExcel)->import('files/'.$docName, function($line) {
            return $line['code'];
        });

        if ($request->storageStage == 'reception') {
            $result = $this->toReceiveTracks($trackCodes);
        }
        elseif ($request->storageStage == 'arrival') {
            $result = $this->toArriveTracks($trackCodes);
        }
        elseif ($request->storageStage == 'giving') {
            $result = $this->toGiveTracks($trackCodes);
        }

        Storage::delete('files/'.$docName);

        return redirect()->back()->with(['result' => $result]);
    }

    public function receptionTracks()
    {
        $fh = fopen('file-manager/tracks/reception-tracks.txt', 'r');

        $trackCodes = [];

        while ($line = fgets($fh)) {
            $trackCodes[] = trim($line);
        }

        fclose($fh);

        $this->toReceiveTracks($trackCodes);
    }

    public function arrivalTracks()
    {
        $fh = fopen('file-manager/tracks/arrival-tracks.txt', 'r');

        $trackCodes = [];

        while ($line = fgets($fh)) {
            $trackCodes[] = trim($line);
        }

        fclose($fh);

        $this->toArriveTracks($trackCodes);
    }

    public function toReceiveTracks($trackCodes)
    {
        $statusReceived = Status::where('slug', 'received')
            ->orWhere('id', 2)
            ->select('id', 'slug')
            ->first();

        $uniqueTrackCodes = collect($trackCodes)->unique();

        $existentTracks = Track::whereIn('code', $uniqueTrackCodes)->get();
        $unreceivedTracks = $existentTracks->where('status', '<', $statusReceived->id);
        $unreceivedTracksStatus = [];

        $receivedTracks = $existentTracks->where('status', '>=', $statusReceived->id);

        $unreceivedTracks->each(function ($item, $key) use (&$unreceivedTracksStatus, $statusReceived) {
            $unreceivedTracksStatus[] = [
                'track_id' => $item->id,
                'status_id' => $statusReceived->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        // Update Unreceived Tracks
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

        $allReceivedTracks = $receivedTracks->merge($unreceivedTracks);

        $nonexistentTracks = collect($trackCodes)->diff($allReceivedTracks->pluck('code'));

        // Create Tracks
        foreach($nonexistentTracks as $code) {

            $newTrack = new Track;
            $newTrack->user_id = null;
            $newTrack->lang = $this->lang;
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

        return [
            'totalTracksCount' => $trackCodes->count(),
            'receivedTracksCount' => $unreceivedTracks->count() + $nonexistentTracks->count(),
            'existentTracksCount' => $receivedTracks->count(),
        ];
    }

    public function toArriveTracks($trackCodes)
    {
        $statusArrived = Status::where('slug', 'arrived')
            ->orWhere('id', 5)
            ->select('id', 'slug')
            ->first();

        $uniqueTrackCodes = collect($trackCodes)->unique();

        // Track::whereIn('code', $trackCodes)->where('status', '<', $statusArrived->id)->get();
        $existentTracks = Track::where('status', '<=', $statusArrived->id)->whereIn('code', $uniqueTrackCodes)->get();
        $unarrivedTracks = $existentTracks->where('status', '<', $statusArrived->id);
        $unarrivedTracksStatus = [];

        $arrivedTracks = $existentTracks->where('status', '>=', $statusArrived->id);

        $region = session()->get('jRegion');

        $unarrivedTracks->each(function ($item, $key) use (&$unarrivedTracksStatus, $statusArrived, $region) {
            $unarrivedTracksStatus[] = [
                'track_id' => $item->id,
                'status_id' => $statusArrived->id,
                'region_id' => $region->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        // Update Unarrived Tracks
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

        $allArrivedTracks = $arrivedTracks->merge($unarrivedTracks);

        $nonexistentTracks = collect($trackCodes)->diff($allArrivedTracks->pluck('code'));

        // Create Tracks
        foreach($nonexistentTracks as $code) {

            $newTrack = new Track;
            $newTrack->user_id = null;
            $newTrack->lang = $this->lang;
            $newTrack->code = $code;
            $newTrack->description = '';
            $newTrack->status  = $statusArrived->id;
            $newTrack->save();

            $trackStatus = new TrackStatus();
            $trackStatus->track_id = $newTrack->id;
            $trackStatus->status_id = $statusArrived->id;
            $trackStatus->region_id = $region->id;
            $trackStatus->created_at = now();
            $trackStatus->updated_at = now();
            $trackStatus->save();
        }

        return [
            'totalTracksCount' => $trackCodes->count(),
            'arrivedTracksCount' => $unarrivedTracks->count() + $nonexistentTracks->count(),
            'existentTracksCount' => $arrivedTracks->count(),
        ];
    }

    public function toGiveTracks($trackCodes)
    {
        $statusGiven = Status::where('slug', 'given')
            ->orWhere('id', 6)
            ->select('id', 'slug')
            ->first();

        $uniqueTrackCodes = collect($trackCodes)->unique();

        // Track::whereIn('code', $trackCodes)->where('status', '<', $statusGiven->id)->get();
        $existentTracks = Track::where('status', '<=', $statusGiven->id)->whereIn('code', $uniqueTrackCodes)->get();
        $ungivenTracks = $existentTracks->where('status', '<', $statusGiven->id);
        $ungivenTracksStatus = [];

        $givenTracks = $existentTracks->where('status', '>=', $statusGiven->id);

        $region = session()->get('jRegion');

        $ungivenTracks->each(function ($item, $key) use (&$ungivenTracksStatus, $statusGiven, $region) {
            $ungivenTracksStatus[] = [
                'track_id' => $item->id,
                'status_id' => $statusGiven->id,
                'region_id' => $region->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        // Update Ungiven Tracks
        if ($ungivenTracks->count() >= 1) {

            try {
                $resultInsert = TrackStatus::insert($ungivenTracksStatus);

                $resultUpdate = Track::whereIn('id', $ungivenTracks->pluck('id')->toArray())
                    ->update(['status' => $statusGiven->id]);

                if (!$resultInsert OR !$resultUpdate) {
                    throw new \Exception("Error Processing Request", 1);
                }
            } catch (\Exception $e) {
                echo 'Error: '.$e->getMessage();
            }
        }

        $allGivenTracks = $givenTracks->merge($ungivenTracks);

        $nonexistentTracks = collect($trackCodes)->diff($allGivenTracks->pluck('code'));

        // Create Tracks
        foreach($nonexistentTracks as $code) {

            $newTrack = new Track;
            $newTrack->user_id = null;
            $newTrack->lang = $this->lang;
            $newTrack->code = $code;
            $newTrack->description = '';
            $newTrack->status  = $statusGiven->id;
            $newTrack->save();

            $trackStatus = new TrackStatus();
            $trackStatus->track_id = $newTrack->id;
            $trackStatus->status_id = $statusGiven->id;
            $trackStatus->region_id = $region->id;
            $trackStatus->created_at = now();
            $trackStatus->updated_at = now();
            $trackStatus->save();
        }

        return [
            'totalTracksCount' => $trackCodes->count(),
            'givenTracksCount' => $ungivenTracks->count() + $nonexistentTracks->count(),
            'existentTracksCount' => $givenTracks->count(),
        ];
    }
}

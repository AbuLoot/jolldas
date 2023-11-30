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
            return $line['Code'] ?? $line['code'];
        });

        if ($request->storageStage == 'reception') {
            $result = $this->toReceiveTracks($trackCodes);
        }
        elseif ($request->storageStage == 'sending') {
            $result = $this->toSendTracks($trackCodes);
        }
        elseif ($request->storageStage == 'sorting') {
            $result = $this->toSortTracks($trackCodes);
        }
        elseif ($request->storageStage == 'sendingLocally') {
            $result = $this->toSendLocallyTracks($trackCodes);
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

    public function exportTracks(Request $request)
    {
        $startDate = $request->startDate ?? date('Y-m-d');
        $endDate = $request->endDate ?? date('Y-m-d');

        $statusSentLocally = Status::select('id', 'slug')
            ->where('slug', 'sent-locally')
            ->orWhere('id', 7)
            ->first();

        $regionId = session()->get('jRegion')->id;
        $regionName = ucfirst(session()->get('jRegion')->slug);

        $sentLocallyTracks = Track::query()
            ->where('status', $statusSentLocally->id)
            ->whereHas('statuses', function($query) use ($regionId) {
                $query->where('region_id', $regionId);
            })
            ->where('updated_at', '>=', $startDate.' 00:00:01')
            ->where('updated_at', '<=', $endDate.' 23:59:59')
            ->get();

        $listTracks = [];

        $sentLocallyTracks->each(function ($item) use (&$listTracks) {
            $listTracks[] = [
                'Code' => $item->code,
                'Description' => $item->description,
                'Text' => $item->text,
            ];
        });

        $listTracks = collect($listTracks);

        $docName = 'Sent locally to '.$regionName.'. Start '.$startDate.' End '.$endDate;

        return (new FastExcel($listTracks))->download($docName.'.xlsx');
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
            // $item = strlen(trim($line)) > 9;
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

        $uniqueTrackCodes = collect($trackCodes)->filter(function ($value) {
                return strlen($value) > 9;
            })->unique();

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

    public function toSendTracks($trackCodes)
    {
        $statusSent = Status::where('slug', 'sent')
            ->orWhere('id', 3)
            ->select('id', 'slug')
            ->first();

        $uniqueTrackCodes = collect($trackCodes)->unique();

        // Track::whereIn('code', $trackCodes)->where('status', '<', $statusSent->id)->get();

        // Get existent tracks
        $existentTracks = Track::query()
                ->where('status', '<=', $statusSent->id)
                ->whereIn('code', $uniqueTrackCodes)
                ->get();

        $unsentTracks = $existentTracks->where('status', '<', $statusSent->id);
        $unsentTracksStatus = [];

        $sentTracks = $existentTracks->where('status', '>=', $statusSent->id);

        $region = session()->get('jRegion');

        $unsentTracks->each(function ($item, $key) use (&$unsentTracksStatus, $statusSent, $region) {
            $unsentTracksStatus[] = [
                'track_id' => $item->id,
                'status_id' => $statusSent->id,
                'region_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        // Update Unarrived Tracks
        if ($unsentTracks->count() >= 1) {

            try {
                $resultInsert = TrackStatus::insert($unsentTracksStatus);

                $resultUpdate = Track::whereIn('id', $unsentTracks->pluck('id')->toArray())
                    ->update(['status' => $statusSent->id]);

                if (!$resultInsert OR !$resultUpdate) {
                    throw new \Exception("Error Processing Request", 1);
                }
            } catch (\Exception $e) {
                echo 'Error: '.$e->getMessage();
            }
        }

        $allSentTracks = $sentTracks->merge($unsentTracks);

        $nonexistentTracks = collect($trackCodes)->diff($allSentTracks->pluck('code'));

        // Create Tracks
        foreach($nonexistentTracks as $code) {

            $newTrack = new Track;
            $newTrack->user_id = null;
            $newTrack->lang = $this->lang;
            $newTrack->code = $code;
            $newTrack->description = '';
            $newTrack->status  = $statusSent->id;
            $newTrack->save();

            $trackStatus = new TrackStatus();
            $trackStatus->track_id = $newTrack->id;
            $trackStatus->status_id = $statusSent->id;
            $trackStatus->region_id = null;
            $trackStatus->created_at = now();
            $trackStatus->updated_at = now();
            $trackStatus->save();
        }

        return [
            'totalTracksCount' => $trackCodes->count(),
            'sentTracksCount' => $unsentTracks->count() + $nonexistentTracks->count(),
            'existentTracksCount' => $sentTracks->count(),
        ];
    }

    public function toSortTracks($trackCodes)
    {
        $statusSorted = Status::where('slug', 'sorted')
            ->orWhere('id', 4)
            ->select('id', 'slug')
            ->first();

        $uniqueTrackCodes = collect($trackCodes)->unique();

        // Track::whereIn('code', $trackCodes)->where('status', '<', $statusSorted->id)->get();

        // Get existent tracks
        $existentTracks = Track::query()
                ->where('status', '<=', $statusSorted->id)
                ->whereIn('code', $uniqueTrackCodes)
                ->get();

        $unsortedTracks = $existentTracks->where('status', '<', $statusSorted->id);
        $unsortedTracksStatus = [];

        $sortedTracks = $existentTracks->where('status', '>=', $statusSorted->id);

        $region = session()->get('jRegion');

        $unsortedTracks->each(function ($item, $key) use (&$unsortedTracksStatus, $statusSorted, $region) {
            $unsortedTracksStatus[] = [
                'track_id' => $item->id,
                'status_id' => $statusSorted->id,
                'region_id' => $region->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        // Update Unarrived Tracks
        if ($unsortedTracks->count() >= 1) {

            try {
                $resultInsert = TrackStatus::insert($unsortedTracksStatus);

                $resultUpdate = Track::whereIn('id', $unsortedTracks->pluck('id')->toArray())
                    ->update(['status' => $statusSorted->id]);

                if (!$resultInsert OR !$resultUpdate) {
                    throw new \Exception("Error Processing Request", 1);
                }
            } catch (\Exception $e) {
                echo 'Error: '.$e->getMessage();
            }
        }

        $allSortedTracks = $sortedTracks->merge($unsortedTracks);

        $nonexistentTracks = collect($trackCodes)->diff($allSortedTracks->pluck('code'));

        // Create Tracks
        foreach($nonexistentTracks as $code) {

            $newTrack = new Track;
            $newTrack->user_id = null;
            $newTrack->lang = $this->lang;
            $newTrack->code = $code;
            $newTrack->description = '';
            $newTrack->status  = $statusSorted->id;
            $newTrack->save();

            $trackStatus = new TrackStatus();
            $trackStatus->track_id = $newTrack->id;
            $trackStatus->status_id = $statusSorted->id;
            $trackStatus->region_id = null;
            $trackStatus->created_at = now();
            $trackStatus->updated_at = now();
            $trackStatus->save();
        }

        return [
            'totalTracksCount' => $trackCodes->count(),
            'sortedTracksCount' => $unsortedTracks->count() + $nonexistentTracks->count(),
            'existentTracksCount' => $sortedTracks->count(),
        ];
    }

    public function toSendLocallyTracks($trackCodes)
    {
        $statusSent = Status::where('slug', 'sent-locally')
            ->orWhere('id', 5)
            ->select('id', 'slug')
            ->first();

        $uniqueTrackCodes = collect($trackCodes)->unique();

        // Track::whereIn('code', $trackCodes)->where('status', '<', $statusSent->id)->get();

        // Get existent tracks
        $existentTracks = Track::query()
                ->where('status', '<=', $statusSent->id)
                ->whereIn('code', $uniqueTrackCodes)
                ->get();

        $unsentTracks = $existentTracks->where('status', '<', $statusSent->id);
        $unsentTracksStatus = [];

        $sentTracks = $existentTracks->where('status', '>=', $statusSent->id);

        $region = session()->get('jRegion');

        $unsentTracks->each(function ($item, $key) use (&$unsentTracksStatus, $statusSent, $region) {
            $unsentTracksStatus[] = [
                'track_id' => $item->id,
                'status_id' => $statusSent->id,
                'region_id' => $region->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        // Update Unarrived Tracks
        if ($unsentTracks->count() >= 1) {

            try {
                $resultInsert = TrackStatus::insert($unsentTracksStatus);

                $resultUpdate = Track::whereIn('id', $unsentTracks->pluck('id')->toArray())
                    ->update(['status' => $statusSent->id]);

                if (!$resultInsert OR !$resultUpdate) {
                    throw new \Exception("Error Processing Request", 1);
                }
            } catch (\Exception $e) {
                echo 'Error: '.$e->getMessage();
            }
        }

        $allSentTracks = $sentTracks->merge($unsentTracks);

        $nonexistentTracks = collect($trackCodes)->diff($allSentTracks->pluck('code'));

        // Create Tracks
        foreach($nonexistentTracks as $code) {

            $newTrack = new Track;
            $newTrack->user_id = null;
            $newTrack->lang = $this->lang;
            $newTrack->code = $code;
            $newTrack->description = '';
            $newTrack->status  = $statusSent->id;
            $newTrack->save();

            $trackStatus = new TrackStatus();
            $trackStatus->track_id = $newTrack->id;
            $trackStatus->status_id = $statusSent->id;
            $trackStatus->region_id = null;
            $trackStatus->created_at = now();
            $trackStatus->updated_at = now();
            $trackStatus->save();
        }

        return [
            'totalTracksCount' => $trackCodes->count(),
            'sentTracksCount' => $unsentTracks->count() + $nonexistentTracks->count(),
            'existentTracksCount' => $sentTracks->count(),
        ];
    }

    public function toArriveTracks($trackCodes)
    {
        $statusArrived = Status::where('slug', 'arrived')
            ->orWhere('id', 6)
            ->select('id', 'slug')
            ->first();

        $uniqueTrackCodes = collect($trackCodes)->unique();

        // Track::whereIn('code', $trackCodes)->where('status', '<', $statusArrived->id)->get();

        // Get existent tracks
        $existentTracks = Track::query()
                ->where('status', '<=', $statusArrived->id)
                ->whereIn('code', $uniqueTrackCodes)
                ->get();

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
            ->orWhere('id', 7)
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

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    public function users(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $response = Http::get('https://randomuser.me/api/?results=' . $limit);
            if ($response->successful()) {
                $userData = $response->json();
                if (empty($userData['results'])) {
                    return response()->json(['error' => 'No user data available'], 404);
                }
                $sortedData = collect($userData['results'])->sortByDesc(function ($user) {
                    return last(explode(' ', $user['name']['last']));
                });
                $xml = new \SimpleXMLElement('<users></users>');
                foreach ($sortedData as $user) {
                    $userXml = $xml->addChild('user');
                    $userXml->addChild('full_name', $user['name']['first'] . ' ' . $user['name']['last']);
                    $userXml->addChild('phone', $user['phone']);
                    $userXml->addChild('email', $user['email']);
                    $userXml->addChild('country', $user['location']['country']);
                }
                $xmlString = $xml->asXML();
                return response($xmlString, 200)->header('Content-Type', 'application/xml');
            } else {
                $activityData = [];
                for ($i = 0; $i < $limit; $i++) {
                    $activityResponse = Http::get('https://www.boredapi.com/api/activity');
                    if ($activityResponse->successful()) {
                        $activity = json_decode($activityResponse->body(), true);
                        $activityData[] = $activity;
                    } else {
                        return response()->json(['error' => 'No data available'], 404);
                    }
                }
                if (empty($activityData)) {
                    return response()->json(['error' => 'No activity data available'], 404);
                }

                usort($activityData, function ($a, $b) {
                    return strcmp($a['type'], $b['type']);
                });

                $xml = new \SimpleXMLElement('<activities></activities>');
                foreach ($activityData as $activity) {
                    $activityXml = $xml->addChild('activity');
                    $activityXml->addChild('activity', $activity['activity']);
                    $activityXml->addChild('type', $activity['type']);
                    $activityXml->addChild('participants', $activity['participants']);
                    $activityXml->addChild('price', $activity['price']);
                    $activityXml->addChild('link', $activity['link']);
                    $activityXml->addChild('key', $activity['key']);
                    $activityXml->addChild('accessibility', $activity['accessibility']);
                }
                $xmlString = $xml->asXML();
                return response($xmlString, 200)->header('Content-Type', 'application/xml');
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred', 'error' => $e], 500);
        }
    }

    // activities api for phpunit
    public function activities(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);

            $activityData = [];
            for ($i = 0; $i < $limit; $i++) {
                $activityResponse = Http::get('https://www.boredapi.com/api/activity');
                if ($activityResponse->successful()) {
                    $activity = json_decode($activityResponse->body(), true);
                    $activityData[] = $activity;
                } else {
                    return response()->json(['error' => 'No data available'], 404);
                }
            }
            if (empty($activityData)) {
                return response()->json(['error' => 'No activity data available'], 404);
            }

            usort($activityData, function ($a, $b) {
                return strcmp($a['type'], $b['type']);
            });

            $xml = new \SimpleXMLElement('<activities></activities>');
            foreach ($activityData as $activity) {
                $activityXml = $xml->addChild('activity');
                $activityXml->addChild('activity', $activity['activity']);
                $activityXml->addChild('type', $activity['type']);
                $activityXml->addChild('participants', $activity['participants']);
                $activityXml->addChild('price', $activity['price']);
                $activityXml->addChild('link', $activity['link']);
                $activityXml->addChild('key', $activity['key']);
                $activityXml->addChild('accessibility', $activity['accessibility']);
            }
            $xmlString = $xml->asXML();
            return response($xmlString, 200)->header('Content-Type', 'application/xml');
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred', 'error' => $e], 500);
        }
    }
}

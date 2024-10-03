<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AbsenceController extends Controller
{
    private mixed $absences;
    private mixed $members;

    public function __construct()
    {
        // Load absences and members from JSON files
        $absencesJson = File::get(base_path('absences.json'));
        $membersJson = File::get(base_path('members.json'));

        $this->absences = json_decode($absencesJson, true)['payload'];
        $this->members = json_decode($membersJson, true)['payload'];
    }

    // Utility function to determine the status of the absence
    private function getStatus($absence): string
    {
        if (!empty($absence['rejectedAt'])) {
            return 'rejected';
        } elseif (!empty($absence['confirmedAt'])) {
            return 'confirmed';
        } else {
            return 'requested';
        }
    }

    // Get member details by userId
    private function getMemberByUserId($userId)
    {
        foreach ($this->members as $member) {
            if ($member['userId'] == $userId) {
                return $member;
            }
        }
        return null;
    }

    // Endpoint to list absences with pagination and filtering
    public function index(Request $request): JsonResponse
    {
        $type = $request->query('type');
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);

        $filteredAbsences = $this->absences;

        // Filter by type if provided
        if ($type) {
            $filteredAbsences = array_filter($filteredAbsences, function($absence) use ($type) {
                return $absence['type'] === $type;
            });
        }

        // Filter by date range if provided
        if ($startDate || $endDate) {
            $filteredAbsences = array_filter($filteredAbsences, function($absence) use ($startDate, $endDate) {
                $absenceStartDate = strtotime($absence['startDate']);
                $absenceEndDate = strtotime($absence['endDate']);

                if ($startDate && strtotime($startDate) > $absenceEndDate) {
                    return false;
                }

                if ($endDate && strtotime($endDate) < $absenceStartDate) {
                    return false;
                }

                return true;
            });
        }

        // Paginate the absences
        $totalAbsences = count($filteredAbsences);
        $offset = ($page - 1) * $limit;
        $paginatedAbsences = array_slice($filteredAbsences, $offset, $limit);

        // Format the response
        $result = array_map(function($absence) {
            $member = $this->getMemberByUserId($absence['userId']);
            return [
                'id' => $absence['id'],
                'member' => $member,  // Include member object
                'type' => $absence['type'],
                'confirmedAt' => $absence['confirmedAt'],
                'createdAt' => $absence['createdAt'],
                'rejectedAt' => $absence['rejectedAt'],
                'startDate' => $absence['startDate'],
                'endDate' => $absence['endDate'],
                'memberNote' => $absence['memberNote'] ?: null,
                'status' => $this->getStatus($absence),
                'admitterNote' => $absence['admitterNote'] ?: null,
            ];
        }, $paginatedAbsences);

        return response()->json([
            'totalAbsences' => $totalAbsences,
            'absences' => $result,
            'page' => (int)$page,
            'limit' => (int)$limit
        ]);
    }

    // Endpoint to get the total number of absences
    public function total(): JsonResponse
    {
        return response()->json(['total' => count($this->absences)]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stackorder extends Model
{
    use HasFactory;

    public function getGetAllMembersFromStackAttribute()
    {

        $stackgroupID = $this->stackgroupID;
        $members = Stackorder::where('stackgroupID', '=', $stackgroupID)->get();

        return $members;
    }

    public function getGetAllMembersBadgeclassidFromStackAttribute()
    {

        $allMembers = $this->get_all_members_from_stack;

        $allMemebersBadgeclassID = $allMembers->map(function ($item, $key) {
            return $item->stackmemberID;
        })->combine($allMembers->map(function ($item, $key) {
            return $item->badgeclassID;
        }));

        return $allMemebersBadgeclassID;
    }

    public function getGetAllMembersRoleFromStackAttribute()
    {

        $allMembers = $this->get_all_members_from_stack;

        $stackgroupID = $this->stackgroupID;

        $allRole = Stackrole::where('stackgroupID', '=', $stackgroupID)->get()->map(function ($item, $key) {
            if ($item->relationship == 'partOf') {
                return [$item->part1 => 'part'];
            } else {
                return [$item->part1 => 'meta'];
            }
        })->unique();

        $filteredRoleList = collect();

        foreach ($allRole->toArray() as $key => $value) {
            $filteredRoleList->put(key($value),current($value));
        }


        return $filteredRoleList;
    }
}

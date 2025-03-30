<?php

namespace App\Repositories\Eloquent;

use App\Models\District;
use App\Models\Province;
use App\Models\Ward;
use App\Repositories\Contracts\LocationRepositoryInterface;

class LocationRepository implements LocationRepositoryInterface
{
    // Triển khai các hàm của Interface
    public function getAll()
    {
        return [
            'province' => Province::all(),
            'district' => District::all(),
            'ward' => Ward::all(),
        ];
    }

    public function getAllProvince()
    {
        return Province::all();
    }

    public function getAllDistrict()
    {
        return District::all();
    }

    public function getAllWard()
    {
        return Ward::all();
    }

    public function getProvinceById($id)
    {
        return Province::find($id);
    }

    public function getDistrictById($id)
    {
        return District::find($id);
    }

    public function getWardById($id)
    {
        return Ward::find($id);
    }

    public function searchProvinces($keyword)
    {
        return Province::where('name', 'like', "%{$keyword}%")
            ->orderBy('name')
            ->get();
    }

    public function getDistrictsByProvince($province_id, $keyword = '')
    {
        $query = District::where('province_id', $province_id);

        if (!empty($keyword)) {
            $query->where('name', 'like', "%{$keyword}%");
        }

        return $query->orderBy('name')->get();
    }

    public function getWardsByDistrict($district_id, $keyword = '')
    {
        $query = Ward::where('district_id', $district_id);

        if (!empty($keyword)) {
            $query->where('name', 'like', "%{$keyword}%");
        }

        return $query->orderBy('name')->get();
    }

    public function searchAddresses($keyword)
    {
        $provinces = Province::where('name', 'like', "%{$keyword}%")->get();
        $districts = District::where('name', 'like', "%{$keyword}%")
            ->with('province')
            ->get();
        $wards = Ward::where('name', 'like', "%{$keyword}%")
            ->with(['district.province'])
            ->get();

        return [
            'provinces' => $provinces,
            'districts' => $districts,
            'wards' => $wards
        ];
    }
}
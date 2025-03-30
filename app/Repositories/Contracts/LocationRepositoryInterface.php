<?php

namespace App\Repositories\Contracts;

interface LocationRepositoryInterface
{
    // Định nghĩa các hàm cần thiết
    public function getAll();

    public function getAllProvince();

    public function getAllDistrict();

    public function getAllWard();

    public function getProvinceById($id);

    public function getDistrictById($id);

    public function getWardById($id);

    public function searchProvinces($keyword);

    public function getDistrictsByProvince($province_id, $keyword = '');

    public function getWardsByDistrict($district_id, $keyword = '');

    public function searchAddresses($keyword);
}
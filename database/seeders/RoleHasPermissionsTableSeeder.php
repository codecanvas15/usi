<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleHasPermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('role_has_permissions')->delete();
        
        \DB::table('role_has_permissions')->insert(array (
            0 => 
            array (
                'permission_id' => 1,
                'role_id' => 10,
            ),
            1 => 
            array (
                'permission_id' => 1,
                'role_id' => 35,
            ),
            2 => 
            array (
                'permission_id' => 1,
                'role_id' => 37,
            ),
            3 => 
            array (
                'permission_id' => 1,
                'role_id' => 38,
            ),
            4 => 
            array (
                'permission_id' => 1,
                'role_id' => 39,
            ),
            5 => 
            array (
                'permission_id' => 1,
                'role_id' => 40,
            ),
            6 => 
            array (
                'permission_id' => 1,
                'role_id' => 41,
            ),
            7 => 
            array (
                'permission_id' => 1,
                'role_id' => 43,
            ),
            8 => 
            array (
                'permission_id' => 1,
                'role_id' => 47,
            ),
            9 => 
            array (
                'permission_id' => 2,
                'role_id' => 10,
            ),
            10 => 
            array (
                'permission_id' => 2,
                'role_id' => 35,
            ),
            11 => 
            array (
                'permission_id' => 2,
                'role_id' => 37,
            ),
            12 => 
            array (
                'permission_id' => 2,
                'role_id' => 43,
            ),
            13 => 
            array (
                'permission_id' => 2,
                'role_id' => 47,
            ),
            14 => 
            array (
                'permission_id' => 3,
                'role_id' => 10,
            ),
            15 => 
            array (
                'permission_id' => 3,
                'role_id' => 35,
            ),
            16 => 
            array (
                'permission_id' => 3,
                'role_id' => 37,
            ),
            17 => 
            array (
                'permission_id' => 3,
                'role_id' => 43,
            ),
            18 => 
            array (
                'permission_id' => 3,
                'role_id' => 47,
            ),
            19 => 
            array (
                'permission_id' => 4,
                'role_id' => 10,
            ),
            20 => 
            array (
                'permission_id' => 4,
                'role_id' => 35,
            ),
            21 => 
            array (
                'permission_id' => 4,
                'role_id' => 37,
            ),
            22 => 
            array (
                'permission_id' => 4,
                'role_id' => 43,
            ),
            23 => 
            array (
                'permission_id' => 4,
                'role_id' => 47,
            ),
            24 => 
            array (
                'permission_id' => 5,
                'role_id' => 10,
            ),
            25 => 
            array (
                'permission_id' => 5,
                'role_id' => 35,
            ),
            26 => 
            array (
                'permission_id' => 5,
                'role_id' => 37,
            ),
            27 => 
            array (
                'permission_id' => 5,
                'role_id' => 43,
            ),
            28 => 
            array (
                'permission_id' => 5,
                'role_id' => 47,
            ),
            29 => 
            array (
                'permission_id' => 6,
                'role_id' => 10,
            ),
            30 => 
            array (
                'permission_id' => 6,
                'role_id' => 35,
            ),
            31 => 
            array (
                'permission_id' => 6,
                'role_id' => 37,
            ),
            32 => 
            array (
                'permission_id' => 6,
                'role_id' => 43,
            ),
            33 => 
            array (
                'permission_id' => 6,
                'role_id' => 47,
            ),
            34 => 
            array (
                'permission_id' => 7,
                'role_id' => 10,
            ),
            35 => 
            array (
                'permission_id' => 7,
                'role_id' => 35,
            ),
            36 => 
            array (
                'permission_id' => 7,
                'role_id' => 37,
            ),
            37 => 
            array (
                'permission_id' => 7,
                'role_id' => 43,
            ),
            38 => 
            array (
                'permission_id' => 7,
                'role_id' => 47,
            ),
            39 => 
            array (
                'permission_id' => 8,
                'role_id' => 10,
            ),
            40 => 
            array (
                'permission_id' => 8,
                'role_id' => 35,
            ),
            41 => 
            array (
                'permission_id' => 8,
                'role_id' => 37,
            ),
            42 => 
            array (
                'permission_id' => 8,
                'role_id' => 43,
            ),
            43 => 
            array (
                'permission_id' => 8,
                'role_id' => 47,
            ),
            44 => 
            array (
                'permission_id' => 9,
                'role_id' => 10,
            ),
            45 => 
            array (
                'permission_id' => 9,
                'role_id' => 35,
            ),
            46 => 
            array (
                'permission_id' => 9,
                'role_id' => 37,
            ),
            47 => 
            array (
                'permission_id' => 9,
                'role_id' => 43,
            ),
            48 => 
            array (
                'permission_id' => 9,
                'role_id' => 47,
            ),
            49 => 
            array (
                'permission_id' => 10,
                'role_id' => 10,
            ),
            50 => 
            array (
                'permission_id' => 10,
                'role_id' => 35,
            ),
            51 => 
            array (
                'permission_id' => 10,
                'role_id' => 37,
            ),
            52 => 
            array (
                'permission_id' => 10,
                'role_id' => 43,
            ),
            53 => 
            array (
                'permission_id' => 10,
                'role_id' => 47,
            ),
            54 => 
            array (
                'permission_id' => 11,
                'role_id' => 10,
            ),
            55 => 
            array (
                'permission_id' => 11,
                'role_id' => 37,
            ),
            56 => 
            array (
                'permission_id' => 11,
                'role_id' => 39,
            ),
            57 => 
            array (
                'permission_id' => 11,
                'role_id' => 40,
            ),
            58 => 
            array (
                'permission_id' => 11,
                'role_id' => 45,
            ),
            59 => 
            array (
                'permission_id' => 11,
                'role_id' => 47,
            ),
            60 => 
            array (
                'permission_id' => 12,
                'role_id' => 10,
            ),
            61 => 
            array (
                'permission_id' => 12,
                'role_id' => 37,
            ),
            62 => 
            array (
                'permission_id' => 12,
                'role_id' => 45,
            ),
            63 => 
            array (
                'permission_id' => 12,
                'role_id' => 47,
            ),
            64 => 
            array (
                'permission_id' => 13,
                'role_id' => 10,
            ),
            65 => 
            array (
                'permission_id' => 13,
                'role_id' => 37,
            ),
            66 => 
            array (
                'permission_id' => 13,
                'role_id' => 45,
            ),
            67 => 
            array (
                'permission_id' => 13,
                'role_id' => 47,
            ),
            68 => 
            array (
                'permission_id' => 14,
                'role_id' => 10,
            ),
            69 => 
            array (
                'permission_id' => 14,
                'role_id' => 37,
            ),
            70 => 
            array (
                'permission_id' => 14,
                'role_id' => 45,
            ),
            71 => 
            array (
                'permission_id' => 14,
                'role_id' => 47,
            ),
            72 => 
            array (
                'permission_id' => 15,
                'role_id' => 10,
            ),
            73 => 
            array (
                'permission_id' => 15,
                'role_id' => 40,
            ),
            74 => 
            array (
                'permission_id' => 15,
                'role_id' => 45,
            ),
            75 => 
            array (
                'permission_id' => 15,
                'role_id' => 46,
            ),
            76 => 
            array (
                'permission_id' => 15,
                'role_id' => 47,
            ),
            77 => 
            array (
                'permission_id' => 16,
                'role_id' => 10,
            ),
            78 => 
            array (
                'permission_id' => 16,
                'role_id' => 45,
            ),
            79 => 
            array (
                'permission_id' => 16,
                'role_id' => 47,
            ),
            80 => 
            array (
                'permission_id' => 17,
                'role_id' => 10,
            ),
            81 => 
            array (
                'permission_id' => 17,
                'role_id' => 45,
            ),
            82 => 
            array (
                'permission_id' => 17,
                'role_id' => 47,
            ),
            83 => 
            array (
                'permission_id' => 18,
                'role_id' => 10,
            ),
            84 => 
            array (
                'permission_id' => 18,
                'role_id' => 45,
            ),
            85 => 
            array (
                'permission_id' => 18,
                'role_id' => 47,
            ),
            86 => 
            array (
                'permission_id' => 19,
                'role_id' => 10,
            ),
            87 => 
            array (
                'permission_id' => 19,
                'role_id' => 45,
            ),
            88 => 
            array (
                'permission_id' => 19,
                'role_id' => 47,
            ),
            89 => 
            array (
                'permission_id' => 20,
                'role_id' => 10,
            ),
            90 => 
            array (
                'permission_id' => 20,
                'role_id' => 45,
            ),
            91 => 
            array (
                'permission_id' => 20,
                'role_id' => 47,
            ),
            92 => 
            array (
                'permission_id' => 21,
                'role_id' => 10,
            ),
            93 => 
            array (
                'permission_id' => 21,
                'role_id' => 35,
            ),
            94 => 
            array (
                'permission_id' => 21,
                'role_id' => 38,
            ),
            95 => 
            array (
                'permission_id' => 21,
                'role_id' => 39,
            ),
            96 => 
            array (
                'permission_id' => 21,
                'role_id' => 40,
            ),
            97 => 
            array (
                'permission_id' => 21,
                'role_id' => 41,
            ),
            98 => 
            array (
                'permission_id' => 21,
                'role_id' => 42,
            ),
            99 => 
            array (
                'permission_id' => 21,
                'role_id' => 45,
            ),
            100 => 
            array (
                'permission_id' => 21,
                'role_id' => 46,
            ),
            101 => 
            array (
                'permission_id' => 21,
                'role_id' => 47,
            ),
            102 => 
            array (
                'permission_id' => 22,
                'role_id' => 10,
            ),
            103 => 
            array (
                'permission_id' => 22,
                'role_id' => 47,
            ),
            104 => 
            array (
                'permission_id' => 23,
                'role_id' => 10,
            ),
            105 => 
            array (
                'permission_id' => 23,
                'role_id' => 47,
            ),
            106 => 
            array (
                'permission_id' => 24,
                'role_id' => 10,
            ),
            107 => 
            array (
                'permission_id' => 24,
                'role_id' => 47,
            ),
            108 => 
            array (
                'permission_id' => 25,
                'role_id' => 10,
            ),
            109 => 
            array (
                'permission_id' => 25,
                'role_id' => 35,
            ),
            110 => 
            array (
                'permission_id' => 25,
                'role_id' => 38,
            ),
            111 => 
            array (
                'permission_id' => 25,
                'role_id' => 39,
            ),
            112 => 
            array (
                'permission_id' => 25,
                'role_id' => 40,
            ),
            113 => 
            array (
                'permission_id' => 25,
                'role_id' => 41,
            ),
            114 => 
            array (
                'permission_id' => 25,
                'role_id' => 42,
            ),
            115 => 
            array (
                'permission_id' => 25,
                'role_id' => 45,
            ),
            116 => 
            array (
                'permission_id' => 25,
                'role_id' => 46,
            ),
            117 => 
            array (
                'permission_id' => 25,
                'role_id' => 47,
            ),
            118 => 
            array (
                'permission_id' => 26,
                'role_id' => 10,
            ),
            119 => 
            array (
                'permission_id' => 26,
                'role_id' => 47,
            ),
            120 => 
            array (
                'permission_id' => 27,
                'role_id' => 10,
            ),
            121 => 
            array (
                'permission_id' => 27,
                'role_id' => 47,
            ),
            122 => 
            array (
                'permission_id' => 28,
                'role_id' => 10,
            ),
            123 => 
            array (
                'permission_id' => 28,
                'role_id' => 47,
            ),
            124 => 
            array (
                'permission_id' => 29,
                'role_id' => 10,
            ),
            125 => 
            array (
                'permission_id' => 29,
                'role_id' => 35,
            ),
            126 => 
            array (
                'permission_id' => 29,
                'role_id' => 38,
            ),
            127 => 
            array (
                'permission_id' => 29,
                'role_id' => 39,
            ),
            128 => 
            array (
                'permission_id' => 29,
                'role_id' => 40,
            ),
            129 => 
            array (
                'permission_id' => 29,
                'role_id' => 41,
            ),
            130 => 
            array (
                'permission_id' => 29,
                'role_id' => 42,
            ),
            131 => 
            array (
                'permission_id' => 29,
                'role_id' => 43,
            ),
            132 => 
            array (
                'permission_id' => 29,
                'role_id' => 45,
            ),
            133 => 
            array (
                'permission_id' => 29,
                'role_id' => 46,
            ),
            134 => 
            array (
                'permission_id' => 29,
                'role_id' => 47,
            ),
            135 => 
            array (
                'permission_id' => 29,
                'role_id' => 48,
            ),
            136 => 
            array (
                'permission_id' => 30,
                'role_id' => 10,
            ),
            137 => 
            array (
                'permission_id' => 30,
                'role_id' => 45,
            ),
            138 => 
            array (
                'permission_id' => 30,
                'role_id' => 47,
            ),
            139 => 
            array (
                'permission_id' => 30,
                'role_id' => 48,
            ),
            140 => 
            array (
                'permission_id' => 31,
                'role_id' => 10,
            ),
            141 => 
            array (
                'permission_id' => 31,
                'role_id' => 45,
            ),
            142 => 
            array (
                'permission_id' => 31,
                'role_id' => 47,
            ),
            143 => 
            array (
                'permission_id' => 32,
                'role_id' => 10,
            ),
            144 => 
            array (
                'permission_id' => 32,
                'role_id' => 45,
            ),
            145 => 
            array (
                'permission_id' => 32,
                'role_id' => 47,
            ),
            146 => 
            array (
                'permission_id' => 33,
                'role_id' => 10,
            ),
            147 => 
            array (
                'permission_id' => 33,
                'role_id' => 38,
            ),
            148 => 
            array (
                'permission_id' => 33,
                'role_id' => 39,
            ),
            149 => 
            array (
                'permission_id' => 33,
                'role_id' => 43,
            ),
            150 => 
            array (
                'permission_id' => 33,
                'role_id' => 47,
            ),
            151 => 
            array (
                'permission_id' => 34,
                'role_id' => 10,
            ),
            152 => 
            array (
                'permission_id' => 34,
                'role_id' => 43,
            ),
            153 => 
            array (
                'permission_id' => 34,
                'role_id' => 47,
            ),
            154 => 
            array (
                'permission_id' => 35,
                'role_id' => 10,
            ),
            155 => 
            array (
                'permission_id' => 35,
                'role_id' => 37,
            ),
            156 => 
            array (
                'permission_id' => 35,
                'role_id' => 38,
            ),
            157 => 
            array (
                'permission_id' => 35,
                'role_id' => 39,
            ),
            158 => 
            array (
                'permission_id' => 35,
                'role_id' => 40,
            ),
            159 => 
            array (
                'permission_id' => 35,
                'role_id' => 41,
            ),
            160 => 
            array (
                'permission_id' => 35,
                'role_id' => 43,
            ),
            161 => 
            array (
                'permission_id' => 35,
                'role_id' => 47,
            ),
            162 => 
            array (
                'permission_id' => 36,
                'role_id' => 10,
            ),
            163 => 
            array (
                'permission_id' => 36,
                'role_id' => 37,
            ),
            164 => 
            array (
                'permission_id' => 36,
                'role_id' => 38,
            ),
            165 => 
            array (
                'permission_id' => 36,
                'role_id' => 40,
            ),
            166 => 
            array (
                'permission_id' => 36,
                'role_id' => 43,
            ),
            167 => 
            array (
                'permission_id' => 36,
                'role_id' => 47,
            ),
            168 => 
            array (
                'permission_id' => 37,
                'role_id' => 10,
            ),
            169 => 
            array (
                'permission_id' => 37,
                'role_id' => 37,
            ),
            170 => 
            array (
                'permission_id' => 37,
                'role_id' => 38,
            ),
            171 => 
            array (
                'permission_id' => 37,
                'role_id' => 40,
            ),
            172 => 
            array (
                'permission_id' => 37,
                'role_id' => 43,
            ),
            173 => 
            array (
                'permission_id' => 37,
                'role_id' => 47,
            ),
            174 => 
            array (
                'permission_id' => 38,
                'role_id' => 10,
            ),
            175 => 
            array (
                'permission_id' => 38,
                'role_id' => 37,
            ),
            176 => 
            array (
                'permission_id' => 38,
                'role_id' => 38,
            ),
            177 => 
            array (
                'permission_id' => 38,
                'role_id' => 40,
            ),
            178 => 
            array (
                'permission_id' => 38,
                'role_id' => 43,
            ),
            179 => 
            array (
                'permission_id' => 38,
                'role_id' => 47,
            ),
            180 => 
            array (
                'permission_id' => 39,
                'role_id' => 10,
            ),
            181 => 
            array (
                'permission_id' => 39,
                'role_id' => 35,
            ),
            182 => 
            array (
                'permission_id' => 39,
                'role_id' => 37,
            ),
            183 => 
            array (
                'permission_id' => 39,
                'role_id' => 38,
            ),
            184 => 
            array (
                'permission_id' => 39,
                'role_id' => 39,
            ),
            185 => 
            array (
                'permission_id' => 39,
                'role_id' => 40,
            ),
            186 => 
            array (
                'permission_id' => 39,
                'role_id' => 41,
            ),
            187 => 
            array (
                'permission_id' => 39,
                'role_id' => 42,
            ),
            188 => 
            array (
                'permission_id' => 39,
                'role_id' => 43,
            ),
            189 => 
            array (
                'permission_id' => 39,
                'role_id' => 47,
            ),
            190 => 
            array (
                'permission_id' => 40,
                'role_id' => 10,
            ),
            191 => 
            array (
                'permission_id' => 40,
                'role_id' => 37,
            ),
            192 => 
            array (
                'permission_id' => 40,
                'role_id' => 38,
            ),
            193 => 
            array (
                'permission_id' => 40,
                'role_id' => 42,
            ),
            194 => 
            array (
                'permission_id' => 40,
                'role_id' => 43,
            ),
            195 => 
            array (
                'permission_id' => 40,
                'role_id' => 47,
            ),
            196 => 
            array (
                'permission_id' => 41,
                'role_id' => 10,
            ),
            197 => 
            array (
                'permission_id' => 41,
                'role_id' => 37,
            ),
            198 => 
            array (
                'permission_id' => 41,
                'role_id' => 38,
            ),
            199 => 
            array (
                'permission_id' => 41,
                'role_id' => 42,
            ),
            200 => 
            array (
                'permission_id' => 41,
                'role_id' => 43,
            ),
            201 => 
            array (
                'permission_id' => 41,
                'role_id' => 47,
            ),
            202 => 
            array (
                'permission_id' => 42,
                'role_id' => 10,
            ),
            203 => 
            array (
                'permission_id' => 42,
                'role_id' => 37,
            ),
            204 => 
            array (
                'permission_id' => 42,
                'role_id' => 38,
            ),
            205 => 
            array (
                'permission_id' => 42,
                'role_id' => 42,
            ),
            206 => 
            array (
                'permission_id' => 42,
                'role_id' => 43,
            ),
            207 => 
            array (
                'permission_id' => 42,
                'role_id' => 47,
            ),
            208 => 
            array (
                'permission_id' => 43,
                'role_id' => 10,
            ),
            209 => 
            array (
                'permission_id' => 43,
                'role_id' => 38,
            ),
            210 => 
            array (
                'permission_id' => 43,
                'role_id' => 45,
            ),
            211 => 
            array (
                'permission_id' => 43,
                'role_id' => 46,
            ),
            212 => 
            array (
                'permission_id' => 43,
                'role_id' => 47,
            ),
            213 => 
            array (
                'permission_id' => 44,
                'role_id' => 10,
            ),
            214 => 
            array (
                'permission_id' => 44,
                'role_id' => 45,
            ),
            215 => 
            array (
                'permission_id' => 44,
                'role_id' => 46,
            ),
            216 => 
            array (
                'permission_id' => 44,
                'role_id' => 47,
            ),
            217 => 
            array (
                'permission_id' => 45,
                'role_id' => 10,
            ),
            218 => 
            array (
                'permission_id' => 45,
                'role_id' => 45,
            ),
            219 => 
            array (
                'permission_id' => 45,
                'role_id' => 47,
            ),
            220 => 
            array (
                'permission_id' => 46,
                'role_id' => 10,
            ),
            221 => 
            array (
                'permission_id' => 46,
                'role_id' => 45,
            ),
            222 => 
            array (
                'permission_id' => 46,
                'role_id' => 47,
            ),
            223 => 
            array (
                'permission_id' => 47,
                'role_id' => 10,
            ),
            224 => 
            array (
                'permission_id' => 47,
                'role_id' => 35,
            ),
            225 => 
            array (
                'permission_id' => 47,
                'role_id' => 38,
            ),
            226 => 
            array (
                'permission_id' => 47,
                'role_id' => 39,
            ),
            227 => 
            array (
                'permission_id' => 47,
                'role_id' => 40,
            ),
            228 => 
            array (
                'permission_id' => 47,
                'role_id' => 41,
            ),
            229 => 
            array (
                'permission_id' => 47,
                'role_id' => 43,
            ),
            230 => 
            array (
                'permission_id' => 47,
                'role_id' => 44,
            ),
            231 => 
            array (
                'permission_id' => 47,
                'role_id' => 47,
            ),
            232 => 
            array (
                'permission_id' => 47,
                'role_id' => 48,
            ),
            233 => 
            array (
                'permission_id' => 48,
                'role_id' => 10,
            ),
            234 => 
            array (
                'permission_id' => 48,
                'role_id' => 43,
            ),
            235 => 
            array (
                'permission_id' => 48,
                'role_id' => 44,
            ),
            236 => 
            array (
                'permission_id' => 48,
                'role_id' => 47,
            ),
            237 => 
            array (
                'permission_id' => 48,
                'role_id' => 48,
            ),
            238 => 
            array (
                'permission_id' => 49,
                'role_id' => 10,
            ),
            239 => 
            array (
                'permission_id' => 49,
                'role_id' => 43,
            ),
            240 => 
            array (
                'permission_id' => 49,
                'role_id' => 44,
            ),
            241 => 
            array (
                'permission_id' => 49,
                'role_id' => 47,
            ),
            242 => 
            array (
                'permission_id' => 49,
                'role_id' => 48,
            ),
            243 => 
            array (
                'permission_id' => 50,
                'role_id' => 10,
            ),
            244 => 
            array (
                'permission_id' => 50,
                'role_id' => 43,
            ),
            245 => 
            array (
                'permission_id' => 50,
                'role_id' => 44,
            ),
            246 => 
            array (
                'permission_id' => 50,
                'role_id' => 47,
            ),
            247 => 
            array (
                'permission_id' => 50,
                'role_id' => 48,
            ),
            248 => 
            array (
                'permission_id' => 51,
                'role_id' => 10,
            ),
            249 => 
            array (
                'permission_id' => 51,
                'role_id' => 43,
            ),
            250 => 
            array (
                'permission_id' => 51,
                'role_id' => 47,
            ),
            251 => 
            array (
                'permission_id' => 51,
                'role_id' => 48,
            ),
            252 => 
            array (
                'permission_id' => 52,
                'role_id' => 10,
            ),
            253 => 
            array (
                'permission_id' => 52,
                'role_id' => 43,
            ),
            254 => 
            array (
                'permission_id' => 52,
                'role_id' => 47,
            ),
            255 => 
            array (
                'permission_id' => 52,
                'role_id' => 48,
            ),
            256 => 
            array (
                'permission_id' => 53,
                'role_id' => 10,
            ),
            257 => 
            array (
                'permission_id' => 53,
                'role_id' => 43,
            ),
            258 => 
            array (
                'permission_id' => 53,
                'role_id' => 47,
            ),
            259 => 
            array (
                'permission_id' => 53,
                'role_id' => 48,
            ),
            260 => 
            array (
                'permission_id' => 54,
                'role_id' => 10,
            ),
            261 => 
            array (
                'permission_id' => 54,
                'role_id' => 43,
            ),
            262 => 
            array (
                'permission_id' => 54,
                'role_id' => 47,
            ),
            263 => 
            array (
                'permission_id' => 54,
                'role_id' => 48,
            ),
            264 => 
            array (
                'permission_id' => 55,
                'role_id' => 10,
            ),
            265 => 
            array (
                'permission_id' => 55,
                'role_id' => 43,
            ),
            266 => 
            array (
                'permission_id' => 55,
                'role_id' => 47,
            ),
            267 => 
            array (
                'permission_id' => 55,
                'role_id' => 48,
            ),
            268 => 
            array (
                'permission_id' => 56,
                'role_id' => 10,
            ),
            269 => 
            array (
                'permission_id' => 56,
                'role_id' => 43,
            ),
            270 => 
            array (
                'permission_id' => 56,
                'role_id' => 47,
            ),
            271 => 
            array (
                'permission_id' => 56,
                'role_id' => 48,
            ),
            272 => 
            array (
                'permission_id' => 57,
                'role_id' => 10,
            ),
            273 => 
            array (
                'permission_id' => 57,
                'role_id' => 43,
            ),
            274 => 
            array (
                'permission_id' => 57,
                'role_id' => 47,
            ),
            275 => 
            array (
                'permission_id' => 57,
                'role_id' => 48,
            ),
            276 => 
            array (
                'permission_id' => 58,
                'role_id' => 10,
            ),
            277 => 
            array (
                'permission_id' => 58,
                'role_id' => 35,
            ),
            278 => 
            array (
                'permission_id' => 58,
                'role_id' => 39,
            ),
            279 => 
            array (
                'permission_id' => 58,
                'role_id' => 40,
            ),
            280 => 
            array (
                'permission_id' => 58,
                'role_id' => 41,
            ),
            281 => 
            array (
                'permission_id' => 58,
                'role_id' => 42,
            ),
            282 => 
            array (
                'permission_id' => 58,
                'role_id' => 43,
            ),
            283 => 
            array (
                'permission_id' => 58,
                'role_id' => 44,
            ),
            284 => 
            array (
                'permission_id' => 58,
                'role_id' => 47,
            ),
            285 => 
            array (
                'permission_id' => 58,
                'role_id' => 48,
            ),
            286 => 
            array (
                'permission_id' => 59,
                'role_id' => 10,
            ),
            287 => 
            array (
                'permission_id' => 59,
                'role_id' => 35,
            ),
            288 => 
            array (
                'permission_id' => 59,
                'role_id' => 42,
            ),
            289 => 
            array (
                'permission_id' => 59,
                'role_id' => 43,
            ),
            290 => 
            array (
                'permission_id' => 59,
                'role_id' => 44,
            ),
            291 => 
            array (
                'permission_id' => 59,
                'role_id' => 47,
            ),
            292 => 
            array (
                'permission_id' => 59,
                'role_id' => 48,
            ),
            293 => 
            array (
                'permission_id' => 60,
                'role_id' => 10,
            ),
            294 => 
            array (
                'permission_id' => 60,
                'role_id' => 35,
            ),
            295 => 
            array (
                'permission_id' => 60,
                'role_id' => 42,
            ),
            296 => 
            array (
                'permission_id' => 60,
                'role_id' => 43,
            ),
            297 => 
            array (
                'permission_id' => 60,
                'role_id' => 44,
            ),
            298 => 
            array (
                'permission_id' => 60,
                'role_id' => 47,
            ),
            299 => 
            array (
                'permission_id' => 60,
                'role_id' => 48,
            ),
            300 => 
            array (
                'permission_id' => 61,
                'role_id' => 10,
            ),
            301 => 
            array (
                'permission_id' => 61,
                'role_id' => 35,
            ),
            302 => 
            array (
                'permission_id' => 61,
                'role_id' => 42,
            ),
            303 => 
            array (
                'permission_id' => 61,
                'role_id' => 43,
            ),
            304 => 
            array (
                'permission_id' => 61,
                'role_id' => 44,
            ),
            305 => 
            array (
                'permission_id' => 61,
                'role_id' => 47,
            ),
            306 => 
            array (
                'permission_id' => 61,
                'role_id' => 48,
            ),
            307 => 
            array (
                'permission_id' => 62,
                'role_id' => 10,
            ),
            308 => 
            array (
                'permission_id' => 62,
                'role_id' => 35,
            ),
            309 => 
            array (
                'permission_id' => 62,
                'role_id' => 43,
            ),
            310 => 
            array (
                'permission_id' => 62,
                'role_id' => 47,
            ),
            311 => 
            array (
                'permission_id' => 62,
                'role_id' => 48,
            ),
            312 => 
            array (
                'permission_id' => 63,
                'role_id' => 10,
            ),
            313 => 
            array (
                'permission_id' => 63,
                'role_id' => 35,
            ),
            314 => 
            array (
                'permission_id' => 63,
                'role_id' => 43,
            ),
            315 => 
            array (
                'permission_id' => 63,
                'role_id' => 47,
            ),
            316 => 
            array (
                'permission_id' => 63,
                'role_id' => 48,
            ),
            317 => 
            array (
                'permission_id' => 64,
                'role_id' => 10,
            ),
            318 => 
            array (
                'permission_id' => 64,
                'role_id' => 35,
            ),
            319 => 
            array (
                'permission_id' => 64,
                'role_id' => 43,
            ),
            320 => 
            array (
                'permission_id' => 64,
                'role_id' => 47,
            ),
            321 => 
            array (
                'permission_id' => 64,
                'role_id' => 48,
            ),
            322 => 
            array (
                'permission_id' => 65,
                'role_id' => 10,
            ),
            323 => 
            array (
                'permission_id' => 65,
                'role_id' => 35,
            ),
            324 => 
            array (
                'permission_id' => 65,
                'role_id' => 43,
            ),
            325 => 
            array (
                'permission_id' => 65,
                'role_id' => 47,
            ),
            326 => 
            array (
                'permission_id' => 65,
                'role_id' => 48,
            ),
            327 => 
            array (
                'permission_id' => 66,
                'role_id' => 10,
            ),
            328 => 
            array (
                'permission_id' => 66,
                'role_id' => 35,
            ),
            329 => 
            array (
                'permission_id' => 66,
                'role_id' => 43,
            ),
            330 => 
            array (
                'permission_id' => 66,
                'role_id' => 47,
            ),
            331 => 
            array (
                'permission_id' => 66,
                'role_id' => 48,
            ),
            332 => 
            array (
                'permission_id' => 67,
                'role_id' => 10,
            ),
            333 => 
            array (
                'permission_id' => 67,
                'role_id' => 35,
            ),
            334 => 
            array (
                'permission_id' => 67,
                'role_id' => 43,
            ),
            335 => 
            array (
                'permission_id' => 67,
                'role_id' => 47,
            ),
            336 => 
            array (
                'permission_id' => 67,
                'role_id' => 48,
            ),
            337 => 
            array (
                'permission_id' => 68,
                'role_id' => 10,
            ),
            338 => 
            array (
                'permission_id' => 68,
                'role_id' => 43,
            ),
            339 => 
            array (
                'permission_id' => 68,
                'role_id' => 47,
            ),
            340 => 
            array (
                'permission_id' => 68,
                'role_id' => 48,
            ),
            341 => 
            array (
                'permission_id' => 69,
                'role_id' => 10,
            ),
            342 => 
            array (
                'permission_id' => 69,
                'role_id' => 35,
            ),
            343 => 
            array (
                'permission_id' => 69,
                'role_id' => 38,
            ),
            344 => 
            array (
                'permission_id' => 69,
                'role_id' => 39,
            ),
            345 => 
            array (
                'permission_id' => 69,
                'role_id' => 40,
            ),
            346 => 
            array (
                'permission_id' => 69,
                'role_id' => 41,
            ),
            347 => 
            array (
                'permission_id' => 69,
                'role_id' => 42,
            ),
            348 => 
            array (
                'permission_id' => 69,
                'role_id' => 47,
            ),
            349 => 
            array (
                'permission_id' => 69,
                'role_id' => 48,
            ),
            350 => 
            array (
                'permission_id' => 70,
                'role_id' => 10,
            ),
            351 => 
            array (
                'permission_id' => 70,
                'role_id' => 35,
            ),
            352 => 
            array (
                'permission_id' => 70,
                'role_id' => 42,
            ),
            353 => 
            array (
                'permission_id' => 70,
                'role_id' => 47,
            ),
            354 => 
            array (
                'permission_id' => 70,
                'role_id' => 48,
            ),
            355 => 
            array (
                'permission_id' => 71,
                'role_id' => 10,
            ),
            356 => 
            array (
                'permission_id' => 71,
                'role_id' => 35,
            ),
            357 => 
            array (
                'permission_id' => 71,
                'role_id' => 42,
            ),
            358 => 
            array (
                'permission_id' => 71,
                'role_id' => 47,
            ),
            359 => 
            array (
                'permission_id' => 71,
                'role_id' => 48,
            ),
            360 => 
            array (
                'permission_id' => 72,
                'role_id' => 10,
            ),
            361 => 
            array (
                'permission_id' => 72,
                'role_id' => 35,
            ),
            362 => 
            array (
                'permission_id' => 72,
                'role_id' => 42,
            ),
            363 => 
            array (
                'permission_id' => 72,
                'role_id' => 47,
            ),
            364 => 
            array (
                'permission_id' => 72,
                'role_id' => 48,
            ),
            365 => 
            array (
                'permission_id' => 73,
                'role_id' => 10,
            ),
            366 => 
            array (
                'permission_id' => 73,
                'role_id' => 35,
            ),
            367 => 
            array (
                'permission_id' => 73,
                'role_id' => 47,
            ),
            368 => 
            array (
                'permission_id' => 73,
                'role_id' => 48,
            ),
            369 => 
            array (
                'permission_id' => 74,
                'role_id' => 10,
            ),
            370 => 
            array (
                'permission_id' => 74,
                'role_id' => 35,
            ),
            371 => 
            array (
                'permission_id' => 74,
                'role_id' => 47,
            ),
            372 => 
            array (
                'permission_id' => 74,
                'role_id' => 48,
            ),
            373 => 
            array (
                'permission_id' => 75,
                'role_id' => 10,
            ),
            374 => 
            array (
                'permission_id' => 75,
                'role_id' => 35,
            ),
            375 => 
            array (
                'permission_id' => 75,
                'role_id' => 47,
            ),
            376 => 
            array (
                'permission_id' => 75,
                'role_id' => 48,
            ),
            377 => 
            array (
                'permission_id' => 76,
                'role_id' => 10,
            ),
            378 => 
            array (
                'permission_id' => 76,
                'role_id' => 35,
            ),
            379 => 
            array (
                'permission_id' => 76,
                'role_id' => 47,
            ),
            380 => 
            array (
                'permission_id' => 76,
                'role_id' => 48,
            ),
            381 => 
            array (
                'permission_id' => 77,
                'role_id' => 10,
            ),
            382 => 
            array (
                'permission_id' => 77,
                'role_id' => 35,
            ),
            383 => 
            array (
                'permission_id' => 77,
                'role_id' => 47,
            ),
            384 => 
            array (
                'permission_id' => 77,
                'role_id' => 48,
            ),
            385 => 
            array (
                'permission_id' => 78,
                'role_id' => 10,
            ),
            386 => 
            array (
                'permission_id' => 78,
                'role_id' => 35,
            ),
            387 => 
            array (
                'permission_id' => 78,
                'role_id' => 47,
            ),
            388 => 
            array (
                'permission_id' => 78,
                'role_id' => 48,
            ),
            389 => 
            array (
                'permission_id' => 79,
                'role_id' => 10,
            ),
            390 => 
            array (
                'permission_id' => 79,
                'role_id' => 47,
            ),
            391 => 
            array (
                'permission_id' => 79,
                'role_id' => 48,
            ),
            392 => 
            array (
                'permission_id' => 80,
                'role_id' => 10,
            ),
            393 => 
            array (
                'permission_id' => 80,
                'role_id' => 38,
            ),
            394 => 
            array (
                'permission_id' => 80,
                'role_id' => 40,
            ),
            395 => 
            array (
                'permission_id' => 80,
                'role_id' => 41,
            ),
            396 => 
            array (
                'permission_id' => 80,
                'role_id' => 43,
            ),
            397 => 
            array (
                'permission_id' => 80,
                'role_id' => 44,
            ),
            398 => 
            array (
                'permission_id' => 80,
                'role_id' => 47,
            ),
            399 => 
            array (
                'permission_id' => 80,
                'role_id' => 48,
            ),
            400 => 
            array (
                'permission_id' => 81,
                'role_id' => 10,
            ),
            401 => 
            array (
                'permission_id' => 81,
                'role_id' => 43,
            ),
            402 => 
            array (
                'permission_id' => 81,
                'role_id' => 44,
            ),
            403 => 
            array (
                'permission_id' => 81,
                'role_id' => 47,
            ),
            404 => 
            array (
                'permission_id' => 81,
                'role_id' => 48,
            ),
            405 => 
            array (
                'permission_id' => 82,
                'role_id' => 10,
            ),
            406 => 
            array (
                'permission_id' => 82,
                'role_id' => 43,
            ),
            407 => 
            array (
                'permission_id' => 82,
                'role_id' => 44,
            ),
            408 => 
            array (
                'permission_id' => 82,
                'role_id' => 47,
            ),
            409 => 
            array (
                'permission_id' => 82,
                'role_id' => 48,
            ),
            410 => 
            array (
                'permission_id' => 83,
                'role_id' => 10,
            ),
            411 => 
            array (
                'permission_id' => 83,
                'role_id' => 43,
            ),
            412 => 
            array (
                'permission_id' => 83,
                'role_id' => 44,
            ),
            413 => 
            array (
                'permission_id' => 83,
                'role_id' => 47,
            ),
            414 => 
            array (
                'permission_id' => 83,
                'role_id' => 48,
            ),
            415 => 
            array (
                'permission_id' => 84,
                'role_id' => 10,
            ),
            416 => 
            array (
                'permission_id' => 84,
                'role_id' => 43,
            ),
            417 => 
            array (
                'permission_id' => 84,
                'role_id' => 44,
            ),
            418 => 
            array (
                'permission_id' => 84,
                'role_id' => 47,
            ),
            419 => 
            array (
                'permission_id' => 84,
                'role_id' => 48,
            ),
            420 => 
            array (
                'permission_id' => 85,
                'role_id' => 10,
            ),
            421 => 
            array (
                'permission_id' => 85,
                'role_id' => 43,
            ),
            422 => 
            array (
                'permission_id' => 85,
                'role_id' => 44,
            ),
            423 => 
            array (
                'permission_id' => 85,
                'role_id' => 47,
            ),
            424 => 
            array (
                'permission_id' => 85,
                'role_id' => 48,
            ),
            425 => 
            array (
                'permission_id' => 86,
                'role_id' => 10,
            ),
            426 => 
            array (
                'permission_id' => 86,
                'role_id' => 43,
            ),
            427 => 
            array (
                'permission_id' => 86,
                'role_id' => 44,
            ),
            428 => 
            array (
                'permission_id' => 86,
                'role_id' => 47,
            ),
            429 => 
            array (
                'permission_id' => 86,
                'role_id' => 48,
            ),
            430 => 
            array (
                'permission_id' => 87,
                'role_id' => 10,
            ),
            431 => 
            array (
                'permission_id' => 87,
                'role_id' => 43,
            ),
            432 => 
            array (
                'permission_id' => 87,
                'role_id' => 44,
            ),
            433 => 
            array (
                'permission_id' => 87,
                'role_id' => 47,
            ),
            434 => 
            array (
                'permission_id' => 87,
                'role_id' => 48,
            ),
            435 => 
            array (
                'permission_id' => 88,
                'role_id' => 10,
            ),
            436 => 
            array (
                'permission_id' => 88,
                'role_id' => 43,
            ),
            437 => 
            array (
                'permission_id' => 88,
                'role_id' => 44,
            ),
            438 => 
            array (
                'permission_id' => 88,
                'role_id' => 47,
            ),
            439 => 
            array (
                'permission_id' => 88,
                'role_id' => 48,
            ),
            440 => 
            array (
                'permission_id' => 89,
                'role_id' => 10,
            ),
            441 => 
            array (
                'permission_id' => 89,
                'role_id' => 43,
            ),
            442 => 
            array (
                'permission_id' => 89,
                'role_id' => 44,
            ),
            443 => 
            array (
                'permission_id' => 89,
                'role_id' => 47,
            ),
            444 => 
            array (
                'permission_id' => 89,
                'role_id' => 48,
            ),
            445 => 
            array (
                'permission_id' => 90,
                'role_id' => 10,
            ),
            446 => 
            array (
                'permission_id' => 90,
                'role_id' => 43,
            ),
            447 => 
            array (
                'permission_id' => 90,
                'role_id' => 44,
            ),
            448 => 
            array (
                'permission_id' => 90,
                'role_id' => 47,
            ),
            449 => 
            array (
                'permission_id' => 90,
                'role_id' => 48,
            ),
            450 => 
            array (
                'permission_id' => 91,
                'role_id' => 10,
            ),
            451 => 
            array (
                'permission_id' => 91,
                'role_id' => 35,
            ),
            452 => 
            array (
                'permission_id' => 91,
                'role_id' => 38,
            ),
            453 => 
            array (
                'permission_id' => 91,
                'role_id' => 40,
            ),
            454 => 
            array (
                'permission_id' => 91,
                'role_id' => 41,
            ),
            455 => 
            array (
                'permission_id' => 91,
                'role_id' => 42,
            ),
            456 => 
            array (
                'permission_id' => 91,
                'role_id' => 43,
            ),
            457 => 
            array (
                'permission_id' => 91,
                'role_id' => 44,
            ),
            458 => 
            array (
                'permission_id' => 91,
                'role_id' => 47,
            ),
            459 => 
            array (
                'permission_id' => 91,
                'role_id' => 48,
            ),
            460 => 
            array (
                'permission_id' => 92,
                'role_id' => 10,
            ),
            461 => 
            array (
                'permission_id' => 92,
                'role_id' => 35,
            ),
            462 => 
            array (
                'permission_id' => 92,
                'role_id' => 42,
            ),
            463 => 
            array (
                'permission_id' => 92,
                'role_id' => 43,
            ),
            464 => 
            array (
                'permission_id' => 92,
                'role_id' => 44,
            ),
            465 => 
            array (
                'permission_id' => 92,
                'role_id' => 47,
            ),
            466 => 
            array (
                'permission_id' => 92,
                'role_id' => 48,
            ),
            467 => 
            array (
                'permission_id' => 93,
                'role_id' => 10,
            ),
            468 => 
            array (
                'permission_id' => 93,
                'role_id' => 35,
            ),
            469 => 
            array (
                'permission_id' => 93,
                'role_id' => 42,
            ),
            470 => 
            array (
                'permission_id' => 93,
                'role_id' => 43,
            ),
            471 => 
            array (
                'permission_id' => 93,
                'role_id' => 44,
            ),
            472 => 
            array (
                'permission_id' => 93,
                'role_id' => 47,
            ),
            473 => 
            array (
                'permission_id' => 93,
                'role_id' => 48,
            ),
            474 => 
            array (
                'permission_id' => 94,
                'role_id' => 10,
            ),
            475 => 
            array (
                'permission_id' => 94,
                'role_id' => 35,
            ),
            476 => 
            array (
                'permission_id' => 94,
                'role_id' => 42,
            ),
            477 => 
            array (
                'permission_id' => 94,
                'role_id' => 43,
            ),
            478 => 
            array (
                'permission_id' => 94,
                'role_id' => 44,
            ),
            479 => 
            array (
                'permission_id' => 94,
                'role_id' => 47,
            ),
            480 => 
            array (
                'permission_id' => 94,
                'role_id' => 48,
            ),
            481 => 
            array (
                'permission_id' => 95,
                'role_id' => 10,
            ),
            482 => 
            array (
                'permission_id' => 95,
                'role_id' => 35,
            ),
            483 => 
            array (
                'permission_id' => 95,
                'role_id' => 43,
            ),
            484 => 
            array (
                'permission_id' => 95,
                'role_id' => 44,
            ),
            485 => 
            array (
                'permission_id' => 95,
                'role_id' => 47,
            ),
            486 => 
            array (
                'permission_id' => 95,
                'role_id' => 48,
            ),
            487 => 
            array (
                'permission_id' => 96,
                'role_id' => 10,
            ),
            488 => 
            array (
                'permission_id' => 96,
                'role_id' => 43,
            ),
            489 => 
            array (
                'permission_id' => 96,
                'role_id' => 44,
            ),
            490 => 
            array (
                'permission_id' => 96,
                'role_id' => 47,
            ),
            491 => 
            array (
                'permission_id' => 96,
                'role_id' => 48,
            ),
            492 => 
            array (
                'permission_id' => 97,
                'role_id' => 10,
            ),
            493 => 
            array (
                'permission_id' => 97,
                'role_id' => 43,
            ),
            494 => 
            array (
                'permission_id' => 97,
                'role_id' => 44,
            ),
            495 => 
            array (
                'permission_id' => 97,
                'role_id' => 47,
            ),
            496 => 
            array (
                'permission_id' => 97,
                'role_id' => 48,
            ),
            497 => 
            array (
                'permission_id' => 98,
                'role_id' => 10,
            ),
            498 => 
            array (
                'permission_id' => 98,
                'role_id' => 43,
            ),
            499 => 
            array (
                'permission_id' => 98,
                'role_id' => 44,
            ),
        ));
        \DB::table('role_has_permissions')->insert(array (
            0 => 
            array (
                'permission_id' => 98,
                'role_id' => 47,
            ),
            1 => 
            array (
                'permission_id' => 98,
                'role_id' => 48,
            ),
            2 => 
            array (
                'permission_id' => 99,
                'role_id' => 10,
            ),
            3 => 
            array (
                'permission_id' => 99,
                'role_id' => 43,
            ),
            4 => 
            array (
                'permission_id' => 99,
                'role_id' => 44,
            ),
            5 => 
            array (
                'permission_id' => 99,
                'role_id' => 47,
            ),
            6 => 
            array (
                'permission_id' => 99,
                'role_id' => 48,
            ),
            7 => 
            array (
                'permission_id' => 100,
                'role_id' => 10,
            ),
            8 => 
            array (
                'permission_id' => 100,
                'role_id' => 43,
            ),
            9 => 
            array (
                'permission_id' => 100,
                'role_id' => 44,
            ),
            10 => 
            array (
                'permission_id' => 100,
                'role_id' => 47,
            ),
            11 => 
            array (
                'permission_id' => 100,
                'role_id' => 48,
            ),
            12 => 
            array (
                'permission_id' => 101,
                'role_id' => 10,
            ),
            13 => 
            array (
                'permission_id' => 101,
                'role_id' => 43,
            ),
            14 => 
            array (
                'permission_id' => 101,
                'role_id' => 44,
            ),
            15 => 
            array (
                'permission_id' => 101,
                'role_id' => 47,
            ),
            16 => 
            array (
                'permission_id' => 101,
                'role_id' => 48,
            ),
            17 => 
            array (
                'permission_id' => 102,
                'role_id' => 10,
            ),
            18 => 
            array (
                'permission_id' => 102,
                'role_id' => 35,
            ),
            19 => 
            array (
                'permission_id' => 102,
                'role_id' => 38,
            ),
            20 => 
            array (
                'permission_id' => 102,
                'role_id' => 39,
            ),
            21 => 
            array (
                'permission_id' => 102,
                'role_id' => 40,
            ),
            22 => 
            array (
                'permission_id' => 102,
                'role_id' => 41,
            ),
            23 => 
            array (
                'permission_id' => 102,
                'role_id' => 42,
            ),
            24 => 
            array (
                'permission_id' => 102,
                'role_id' => 43,
            ),
            25 => 
            array (
                'permission_id' => 102,
                'role_id' => 47,
            ),
            26 => 
            array (
                'permission_id' => 103,
                'role_id' => 10,
            ),
            27 => 
            array (
                'permission_id' => 103,
                'role_id' => 35,
            ),
            28 => 
            array (
                'permission_id' => 103,
                'role_id' => 42,
            ),
            29 => 
            array (
                'permission_id' => 103,
                'role_id' => 43,
            ),
            30 => 
            array (
                'permission_id' => 103,
                'role_id' => 47,
            ),
            31 => 
            array (
                'permission_id' => 104,
                'role_id' => 10,
            ),
            32 => 
            array (
                'permission_id' => 104,
                'role_id' => 35,
            ),
            33 => 
            array (
                'permission_id' => 104,
                'role_id' => 42,
            ),
            34 => 
            array (
                'permission_id' => 104,
                'role_id' => 43,
            ),
            35 => 
            array (
                'permission_id' => 104,
                'role_id' => 47,
            ),
            36 => 
            array (
                'permission_id' => 105,
                'role_id' => 10,
            ),
            37 => 
            array (
                'permission_id' => 105,
                'role_id' => 35,
            ),
            38 => 
            array (
                'permission_id' => 105,
                'role_id' => 43,
            ),
            39 => 
            array (
                'permission_id' => 105,
                'role_id' => 47,
            ),
            40 => 
            array (
                'permission_id' => 106,
                'role_id' => 10,
            ),
            41 => 
            array (
                'permission_id' => 106,
                'role_id' => 43,
            ),
            42 => 
            array (
                'permission_id' => 106,
                'role_id' => 44,
            ),
            43 => 
            array (
                'permission_id' => 106,
                'role_id' => 47,
            ),
            44 => 
            array (
                'permission_id' => 106,
                'role_id' => 48,
            ),
            45 => 
            array (
                'permission_id' => 107,
                'role_id' => 10,
            ),
            46 => 
            array (
                'permission_id' => 107,
                'role_id' => 43,
            ),
            47 => 
            array (
                'permission_id' => 107,
                'role_id' => 44,
            ),
            48 => 
            array (
                'permission_id' => 107,
                'role_id' => 47,
            ),
            49 => 
            array (
                'permission_id' => 107,
                'role_id' => 48,
            ),
            50 => 
            array (
                'permission_id' => 108,
                'role_id' => 10,
            ),
            51 => 
            array (
                'permission_id' => 108,
                'role_id' => 43,
            ),
            52 => 
            array (
                'permission_id' => 108,
                'role_id' => 44,
            ),
            53 => 
            array (
                'permission_id' => 108,
                'role_id' => 47,
            ),
            54 => 
            array (
                'permission_id' => 108,
                'role_id' => 48,
            ),
            55 => 
            array (
                'permission_id' => 109,
                'role_id' => 10,
            ),
            56 => 
            array (
                'permission_id' => 109,
                'role_id' => 43,
            ),
            57 => 
            array (
                'permission_id' => 109,
                'role_id' => 47,
            ),
            58 => 
            array (
                'permission_id' => 109,
                'role_id' => 48,
            ),
            59 => 
            array (
                'permission_id' => 110,
                'role_id' => 10,
            ),
            60 => 
            array (
                'permission_id' => 110,
                'role_id' => 43,
            ),
            61 => 
            array (
                'permission_id' => 110,
                'role_id' => 47,
            ),
            62 => 
            array (
                'permission_id' => 111,
                'role_id' => 10,
            ),
            63 => 
            array (
                'permission_id' => 111,
                'role_id' => 43,
            ),
            64 => 
            array (
                'permission_id' => 111,
                'role_id' => 47,
            ),
            65 => 
            array (
                'permission_id' => 111,
                'role_id' => 48,
            ),
            66 => 
            array (
                'permission_id' => 112,
                'role_id' => 10,
            ),
            67 => 
            array (
                'permission_id' => 112,
                'role_id' => 35,
            ),
            68 => 
            array (
                'permission_id' => 112,
                'role_id' => 43,
            ),
            69 => 
            array (
                'permission_id' => 112,
                'role_id' => 44,
            ),
            70 => 
            array (
                'permission_id' => 112,
                'role_id' => 47,
            ),
            71 => 
            array (
                'permission_id' => 113,
                'role_id' => 10,
            ),
            72 => 
            array (
                'permission_id' => 113,
                'role_id' => 35,
            ),
            73 => 
            array (
                'permission_id' => 113,
                'role_id' => 43,
            ),
            74 => 
            array (
                'permission_id' => 113,
                'role_id' => 44,
            ),
            75 => 
            array (
                'permission_id' => 113,
                'role_id' => 47,
            ),
            76 => 
            array (
                'permission_id' => 114,
                'role_id' => 10,
            ),
            77 => 
            array (
                'permission_id' => 114,
                'role_id' => 35,
            ),
            78 => 
            array (
                'permission_id' => 114,
                'role_id' => 43,
            ),
            79 => 
            array (
                'permission_id' => 114,
                'role_id' => 44,
            ),
            80 => 
            array (
                'permission_id' => 114,
                'role_id' => 47,
            ),
            81 => 
            array (
                'permission_id' => 115,
                'role_id' => 10,
            ),
            82 => 
            array (
                'permission_id' => 115,
                'role_id' => 43,
            ),
            83 => 
            array (
                'permission_id' => 115,
                'role_id' => 44,
            ),
            84 => 
            array (
                'permission_id' => 115,
                'role_id' => 47,
            ),
            85 => 
            array (
                'permission_id' => 115,
                'role_id' => 48,
            ),
            86 => 
            array (
                'permission_id' => 116,
                'role_id' => 10,
            ),
            87 => 
            array (
                'permission_id' => 116,
                'role_id' => 43,
            ),
            88 => 
            array (
                'permission_id' => 116,
                'role_id' => 44,
            ),
            89 => 
            array (
                'permission_id' => 116,
                'role_id' => 47,
            ),
            90 => 
            array (
                'permission_id' => 117,
                'role_id' => 10,
            ),
            91 => 
            array (
                'permission_id' => 117,
                'role_id' => 43,
            ),
            92 => 
            array (
                'permission_id' => 117,
                'role_id' => 44,
            ),
            93 => 
            array (
                'permission_id' => 117,
                'role_id' => 47,
            ),
            94 => 
            array (
                'permission_id' => 117,
                'role_id' => 48,
            ),
            95 => 
            array (
                'permission_id' => 118,
                'role_id' => 10,
            ),
            96 => 
            array (
                'permission_id' => 118,
                'role_id' => 35,
            ),
            97 => 
            array (
                'permission_id' => 118,
                'role_id' => 42,
            ),
            98 => 
            array (
                'permission_id' => 118,
                'role_id' => 43,
            ),
            99 => 
            array (
                'permission_id' => 118,
                'role_id' => 44,
            ),
            100 => 
            array (
                'permission_id' => 118,
                'role_id' => 47,
            ),
            101 => 
            array (
                'permission_id' => 118,
                'role_id' => 48,
            ),
            102 => 
            array (
                'permission_id' => 119,
                'role_id' => 10,
            ),
            103 => 
            array (
                'permission_id' => 119,
                'role_id' => 35,
            ),
            104 => 
            array (
                'permission_id' => 119,
                'role_id' => 42,
            ),
            105 => 
            array (
                'permission_id' => 119,
                'role_id' => 43,
            ),
            106 => 
            array (
                'permission_id' => 119,
                'role_id' => 44,
            ),
            107 => 
            array (
                'permission_id' => 119,
                'role_id' => 47,
            ),
            108 => 
            array (
                'permission_id' => 119,
                'role_id' => 48,
            ),
            109 => 
            array (
                'permission_id' => 120,
                'role_id' => 10,
            ),
            110 => 
            array (
                'permission_id' => 120,
                'role_id' => 35,
            ),
            111 => 
            array (
                'permission_id' => 120,
                'role_id' => 42,
            ),
            112 => 
            array (
                'permission_id' => 120,
                'role_id' => 43,
            ),
            113 => 
            array (
                'permission_id' => 120,
                'role_id' => 44,
            ),
            114 => 
            array (
                'permission_id' => 120,
                'role_id' => 47,
            ),
            115 => 
            array (
                'permission_id' => 120,
                'role_id' => 48,
            ),
            116 => 
            array (
                'permission_id' => 121,
                'role_id' => 10,
            ),
            117 => 
            array (
                'permission_id' => 121,
                'role_id' => 35,
            ),
            118 => 
            array (
                'permission_id' => 121,
                'role_id' => 42,
            ),
            119 => 
            array (
                'permission_id' => 121,
                'role_id' => 43,
            ),
            120 => 
            array (
                'permission_id' => 121,
                'role_id' => 44,
            ),
            121 => 
            array (
                'permission_id' => 121,
                'role_id' => 47,
            ),
            122 => 
            array (
                'permission_id' => 121,
                'role_id' => 48,
            ),
            123 => 
            array (
                'permission_id' => 122,
                'role_id' => 10,
            ),
            124 => 
            array (
                'permission_id' => 122,
                'role_id' => 35,
            ),
            125 => 
            array (
                'permission_id' => 122,
                'role_id' => 42,
            ),
            126 => 
            array (
                'permission_id' => 122,
                'role_id' => 43,
            ),
            127 => 
            array (
                'permission_id' => 122,
                'role_id' => 44,
            ),
            128 => 
            array (
                'permission_id' => 122,
                'role_id' => 47,
            ),
            129 => 
            array (
                'permission_id' => 123,
                'role_id' => 10,
            ),
            130 => 
            array (
                'permission_id' => 123,
                'role_id' => 35,
            ),
            131 => 
            array (
                'permission_id' => 123,
                'role_id' => 42,
            ),
            132 => 
            array (
                'permission_id' => 123,
                'role_id' => 43,
            ),
            133 => 
            array (
                'permission_id' => 123,
                'role_id' => 44,
            ),
            134 => 
            array (
                'permission_id' => 123,
                'role_id' => 47,
            ),
            135 => 
            array (
                'permission_id' => 124,
                'role_id' => 10,
            ),
            136 => 
            array (
                'permission_id' => 124,
                'role_id' => 35,
            ),
            137 => 
            array (
                'permission_id' => 124,
                'role_id' => 42,
            ),
            138 => 
            array (
                'permission_id' => 124,
                'role_id' => 43,
            ),
            139 => 
            array (
                'permission_id' => 124,
                'role_id' => 44,
            ),
            140 => 
            array (
                'permission_id' => 124,
                'role_id' => 47,
            ),
            141 => 
            array (
                'permission_id' => 125,
                'role_id' => 10,
            ),
            142 => 
            array (
                'permission_id' => 125,
                'role_id' => 35,
            ),
            143 => 
            array (
                'permission_id' => 125,
                'role_id' => 43,
            ),
            144 => 
            array (
                'permission_id' => 125,
                'role_id' => 44,
            ),
            145 => 
            array (
                'permission_id' => 125,
                'role_id' => 47,
            ),
            146 => 
            array (
                'permission_id' => 126,
                'role_id' => 10,
            ),
            147 => 
            array (
                'permission_id' => 126,
                'role_id' => 35,
            ),
            148 => 
            array (
                'permission_id' => 126,
                'role_id' => 43,
            ),
            149 => 
            array (
                'permission_id' => 126,
                'role_id' => 44,
            ),
            150 => 
            array (
                'permission_id' => 126,
                'role_id' => 47,
            ),
            151 => 
            array (
                'permission_id' => 127,
                'role_id' => 10,
            ),
            152 => 
            array (
                'permission_id' => 127,
                'role_id' => 35,
            ),
            153 => 
            array (
                'permission_id' => 127,
                'role_id' => 42,
            ),
            154 => 
            array (
                'permission_id' => 127,
                'role_id' => 43,
            ),
            155 => 
            array (
                'permission_id' => 127,
                'role_id' => 44,
            ),
            156 => 
            array (
                'permission_id' => 127,
                'role_id' => 47,
            ),
            157 => 
            array (
                'permission_id' => 127,
                'role_id' => 48,
            ),
            158 => 
            array (
                'permission_id' => 128,
                'role_id' => 10,
            ),
            159 => 
            array (
                'permission_id' => 128,
                'role_id' => 35,
            ),
            160 => 
            array (
                'permission_id' => 128,
                'role_id' => 42,
            ),
            161 => 
            array (
                'permission_id' => 128,
                'role_id' => 43,
            ),
            162 => 
            array (
                'permission_id' => 128,
                'role_id' => 44,
            ),
            163 => 
            array (
                'permission_id' => 128,
                'role_id' => 47,
            ),
            164 => 
            array (
                'permission_id' => 128,
                'role_id' => 48,
            ),
            165 => 
            array (
                'permission_id' => 129,
                'role_id' => 10,
            ),
            166 => 
            array (
                'permission_id' => 129,
                'role_id' => 35,
            ),
            167 => 
            array (
                'permission_id' => 129,
                'role_id' => 43,
            ),
            168 => 
            array (
                'permission_id' => 129,
                'role_id' => 44,
            ),
            169 => 
            array (
                'permission_id' => 129,
                'role_id' => 47,
            ),
            170 => 
            array (
                'permission_id' => 129,
                'role_id' => 48,
            ),
            171 => 
            array (
                'permission_id' => 130,
                'role_id' => 10,
            ),
            172 => 
            array (
                'permission_id' => 130,
                'role_id' => 35,
            ),
            173 => 
            array (
                'permission_id' => 130,
                'role_id' => 43,
            ),
            174 => 
            array (
                'permission_id' => 130,
                'role_id' => 44,
            ),
            175 => 
            array (
                'permission_id' => 130,
                'role_id' => 47,
            ),
            176 => 
            array (
                'permission_id' => 130,
                'role_id' => 48,
            ),
            177 => 
            array (
                'permission_id' => 131,
                'role_id' => 10,
            ),
            178 => 
            array (
                'permission_id' => 131,
                'role_id' => 43,
            ),
            179 => 
            array (
                'permission_id' => 131,
                'role_id' => 44,
            ),
            180 => 
            array (
                'permission_id' => 131,
                'role_id' => 47,
            ),
            181 => 
            array (
                'permission_id' => 131,
                'role_id' => 48,
            ),
            182 => 
            array (
                'permission_id' => 132,
                'role_id' => 10,
            ),
            183 => 
            array (
                'permission_id' => 132,
                'role_id' => 43,
            ),
            184 => 
            array (
                'permission_id' => 132,
                'role_id' => 44,
            ),
            185 => 
            array (
                'permission_id' => 132,
                'role_id' => 47,
            ),
            186 => 
            array (
                'permission_id' => 133,
                'role_id' => 10,
            ),
            187 => 
            array (
                'permission_id' => 133,
                'role_id' => 43,
            ),
            188 => 
            array (
                'permission_id' => 133,
                'role_id' => 44,
            ),
            189 => 
            array (
                'permission_id' => 133,
                'role_id' => 47,
            ),
            190 => 
            array (
                'permission_id' => 134,
                'role_id' => 10,
            ),
            191 => 
            array (
                'permission_id' => 134,
                'role_id' => 43,
            ),
            192 => 
            array (
                'permission_id' => 134,
                'role_id' => 44,
            ),
            193 => 
            array (
                'permission_id' => 134,
                'role_id' => 47,
            ),
            194 => 
            array (
                'permission_id' => 135,
                'role_id' => 10,
            ),
            195 => 
            array (
                'permission_id' => 135,
                'role_id' => 43,
            ),
            196 => 
            array (
                'permission_id' => 135,
                'role_id' => 44,
            ),
            197 => 
            array (
                'permission_id' => 135,
                'role_id' => 47,
            ),
            198 => 
            array (
                'permission_id' => 136,
                'role_id' => 10,
            ),
            199 => 
            array (
                'permission_id' => 136,
                'role_id' => 43,
            ),
            200 => 
            array (
                'permission_id' => 136,
                'role_id' => 44,
            ),
            201 => 
            array (
                'permission_id' => 136,
                'role_id' => 47,
            ),
            202 => 
            array (
                'permission_id' => 136,
                'role_id' => 48,
            ),
            203 => 
            array (
                'permission_id' => 137,
                'role_id' => 10,
            ),
            204 => 
            array (
                'permission_id' => 137,
                'role_id' => 43,
            ),
            205 => 
            array (
                'permission_id' => 137,
                'role_id' => 44,
            ),
            206 => 
            array (
                'permission_id' => 137,
                'role_id' => 47,
            ),
            207 => 
            array (
                'permission_id' => 137,
                'role_id' => 48,
            ),
            208 => 
            array (
                'permission_id' => 138,
                'role_id' => 10,
            ),
            209 => 
            array (
                'permission_id' => 138,
                'role_id' => 43,
            ),
            210 => 
            array (
                'permission_id' => 138,
                'role_id' => 44,
            ),
            211 => 
            array (
                'permission_id' => 138,
                'role_id' => 47,
            ),
            212 => 
            array (
                'permission_id' => 138,
                'role_id' => 48,
            ),
            213 => 
            array (
                'permission_id' => 139,
                'role_id' => 10,
            ),
            214 => 
            array (
                'permission_id' => 139,
                'role_id' => 43,
            ),
            215 => 
            array (
                'permission_id' => 139,
                'role_id' => 44,
            ),
            216 => 
            array (
                'permission_id' => 139,
                'role_id' => 47,
            ),
            217 => 
            array (
                'permission_id' => 139,
                'role_id' => 48,
            ),
            218 => 
            array (
                'permission_id' => 140,
                'role_id' => 10,
            ),
            219 => 
            array (
                'permission_id' => 140,
                'role_id' => 45,
            ),
            220 => 
            array (
                'permission_id' => 140,
                'role_id' => 46,
            ),
            221 => 
            array (
                'permission_id' => 140,
                'role_id' => 47,
            ),
            222 => 
            array (
                'permission_id' => 141,
                'role_id' => 10,
            ),
            223 => 
            array (
                'permission_id' => 141,
                'role_id' => 45,
            ),
            224 => 
            array (
                'permission_id' => 141,
                'role_id' => 46,
            ),
            225 => 
            array (
                'permission_id' => 141,
                'role_id' => 47,
            ),
            226 => 
            array (
                'permission_id' => 142,
                'role_id' => 10,
            ),
            227 => 
            array (
                'permission_id' => 142,
                'role_id' => 45,
            ),
            228 => 
            array (
                'permission_id' => 142,
                'role_id' => 46,
            ),
            229 => 
            array (
                'permission_id' => 142,
                'role_id' => 47,
            ),
            230 => 
            array (
                'permission_id' => 143,
                'role_id' => 10,
            ),
            231 => 
            array (
                'permission_id' => 143,
                'role_id' => 45,
            ),
            232 => 
            array (
                'permission_id' => 143,
                'role_id' => 47,
            ),
            233 => 
            array (
                'permission_id' => 144,
                'role_id' => 10,
            ),
            234 => 
            array (
                'permission_id' => 144,
                'role_id' => 45,
            ),
            235 => 
            array (
                'permission_id' => 144,
                'role_id' => 46,
            ),
            236 => 
            array (
                'permission_id' => 144,
                'role_id' => 47,
            ),
            237 => 
            array (
                'permission_id' => 145,
                'role_id' => 10,
            ),
            238 => 
            array (
                'permission_id' => 145,
                'role_id' => 45,
            ),
            239 => 
            array (
                'permission_id' => 145,
                'role_id' => 46,
            ),
            240 => 
            array (
                'permission_id' => 145,
                'role_id' => 47,
            ),
            241 => 
            array (
                'permission_id' => 146,
                'role_id' => 10,
            ),
            242 => 
            array (
                'permission_id' => 146,
                'role_id' => 45,
            ),
            243 => 
            array (
                'permission_id' => 146,
                'role_id' => 46,
            ),
            244 => 
            array (
                'permission_id' => 146,
                'role_id' => 47,
            ),
            245 => 
            array (
                'permission_id' => 147,
                'role_id' => 10,
            ),
            246 => 
            array (
                'permission_id' => 147,
                'role_id' => 45,
            ),
            247 => 
            array (
                'permission_id' => 147,
                'role_id' => 46,
            ),
            248 => 
            array (
                'permission_id' => 147,
                'role_id' => 47,
            ),
            249 => 
            array (
                'permission_id' => 148,
                'role_id' => 10,
            ),
            250 => 
            array (
                'permission_id' => 148,
                'role_id' => 45,
            ),
            251 => 
            array (
                'permission_id' => 148,
                'role_id' => 46,
            ),
            252 => 
            array (
                'permission_id' => 148,
                'role_id' => 47,
            ),
            253 => 
            array (
                'permission_id' => 149,
                'role_id' => 10,
            ),
            254 => 
            array (
                'permission_id' => 149,
                'role_id' => 45,
            ),
            255 => 
            array (
                'permission_id' => 149,
                'role_id' => 47,
            ),
            256 => 
            array (
                'permission_id' => 150,
                'role_id' => 10,
            ),
            257 => 
            array (
                'permission_id' => 150,
                'role_id' => 45,
            ),
            258 => 
            array (
                'permission_id' => 150,
                'role_id' => 46,
            ),
            259 => 
            array (
                'permission_id' => 150,
                'role_id' => 47,
            ),
            260 => 
            array (
                'permission_id' => 151,
                'role_id' => 10,
            ),
            261 => 
            array (
                'permission_id' => 151,
                'role_id' => 45,
            ),
            262 => 
            array (
                'permission_id' => 151,
                'role_id' => 46,
            ),
            263 => 
            array (
                'permission_id' => 151,
                'role_id' => 47,
            ),
            264 => 
            array (
                'permission_id' => 152,
                'role_id' => 10,
            ),
            265 => 
            array (
                'permission_id' => 152,
                'role_id' => 45,
            ),
            266 => 
            array (
                'permission_id' => 152,
                'role_id' => 46,
            ),
            267 => 
            array (
                'permission_id' => 152,
                'role_id' => 47,
            ),
            268 => 
            array (
                'permission_id' => 153,
                'role_id' => 10,
            ),
            269 => 
            array (
                'permission_id' => 153,
                'role_id' => 45,
            ),
            270 => 
            array (
                'permission_id' => 153,
                'role_id' => 47,
            ),
            271 => 
            array (
                'permission_id' => 154,
                'role_id' => 10,
            ),
            272 => 
            array (
                'permission_id' => 154,
                'role_id' => 45,
            ),
            273 => 
            array (
                'permission_id' => 154,
                'role_id' => 46,
            ),
            274 => 
            array (
                'permission_id' => 154,
                'role_id' => 47,
            ),
            275 => 
            array (
                'permission_id' => 155,
                'role_id' => 10,
            ),
            276 => 
            array (
                'permission_id' => 155,
                'role_id' => 45,
            ),
            277 => 
            array (
                'permission_id' => 155,
                'role_id' => 46,
            ),
            278 => 
            array (
                'permission_id' => 155,
                'role_id' => 47,
            ),
            279 => 
            array (
                'permission_id' => 156,
                'role_id' => 10,
            ),
            280 => 
            array (
                'permission_id' => 156,
                'role_id' => 45,
            ),
            281 => 
            array (
                'permission_id' => 156,
                'role_id' => 46,
            ),
            282 => 
            array (
                'permission_id' => 156,
                'role_id' => 47,
            ),
            283 => 
            array (
                'permission_id' => 157,
                'role_id' => 10,
            ),
            284 => 
            array (
                'permission_id' => 157,
                'role_id' => 45,
            ),
            285 => 
            array (
                'permission_id' => 157,
                'role_id' => 47,
            ),
            286 => 
            array (
                'permission_id' => 158,
                'role_id' => 10,
            ),
            287 => 
            array (
                'permission_id' => 158,
                'role_id' => 45,
            ),
            288 => 
            array (
                'permission_id' => 158,
                'role_id' => 47,
            ),
            289 => 
            array (
                'permission_id' => 159,
                'role_id' => 10,
            ),
            290 => 
            array (
                'permission_id' => 159,
                'role_id' => 45,
            ),
            291 => 
            array (
                'permission_id' => 159,
                'role_id' => 47,
            ),
            292 => 
            array (
                'permission_id' => 160,
                'role_id' => 10,
            ),
            293 => 
            array (
                'permission_id' => 160,
                'role_id' => 45,
            ),
            294 => 
            array (
                'permission_id' => 160,
                'role_id' => 47,
            ),
            295 => 
            array (
                'permission_id' => 161,
                'role_id' => 10,
            ),
            296 => 
            array (
                'permission_id' => 161,
                'role_id' => 45,
            ),
            297 => 
            array (
                'permission_id' => 161,
                'role_id' => 47,
            ),
            298 => 
            array (
                'permission_id' => 162,
                'role_id' => 10,
            ),
            299 => 
            array (
                'permission_id' => 162,
                'role_id' => 45,
            ),
            300 => 
            array (
                'permission_id' => 162,
                'role_id' => 47,
            ),
            301 => 
            array (
                'permission_id' => 163,
                'role_id' => 10,
            ),
            302 => 
            array (
                'permission_id' => 163,
                'role_id' => 45,
            ),
            303 => 
            array (
                'permission_id' => 163,
                'role_id' => 47,
            ),
            304 => 
            array (
                'permission_id' => 164,
                'role_id' => 10,
            ),
            305 => 
            array (
                'permission_id' => 164,
                'role_id' => 45,
            ),
            306 => 
            array (
                'permission_id' => 164,
                'role_id' => 47,
            ),
            307 => 
            array (
                'permission_id' => 165,
                'role_id' => 10,
            ),
            308 => 
            array (
                'permission_id' => 165,
                'role_id' => 45,
            ),
            309 => 
            array (
                'permission_id' => 165,
                'role_id' => 47,
            ),
            310 => 
            array (
                'permission_id' => 170,
                'role_id' => 10,
            ),
            311 => 
            array (
                'permission_id' => 170,
                'role_id' => 45,
            ),
            312 => 
            array (
                'permission_id' => 170,
                'role_id' => 46,
            ),
            313 => 
            array (
                'permission_id' => 170,
                'role_id' => 47,
            ),
            314 => 
            array (
                'permission_id' => 171,
                'role_id' => 10,
            ),
            315 => 
            array (
                'permission_id' => 171,
                'role_id' => 45,
            ),
            316 => 
            array (
                'permission_id' => 171,
                'role_id' => 46,
            ),
            317 => 
            array (
                'permission_id' => 171,
                'role_id' => 47,
            ),
            318 => 
            array (
                'permission_id' => 172,
                'role_id' => 10,
            ),
            319 => 
            array (
                'permission_id' => 172,
                'role_id' => 45,
            ),
            320 => 
            array (
                'permission_id' => 172,
                'role_id' => 46,
            ),
            321 => 
            array (
                'permission_id' => 172,
                'role_id' => 47,
            ),
            322 => 
            array (
                'permission_id' => 173,
                'role_id' => 10,
            ),
            323 => 
            array (
                'permission_id' => 173,
                'role_id' => 45,
            ),
            324 => 
            array (
                'permission_id' => 173,
                'role_id' => 47,
            ),
            325 => 
            array (
                'permission_id' => 174,
                'role_id' => 10,
            ),
            326 => 
            array (
                'permission_id' => 174,
                'role_id' => 35,
            ),
            327 => 
            array (
                'permission_id' => 174,
                'role_id' => 37,
            ),
            328 => 
            array (
                'permission_id' => 174,
                'role_id' => 38,
            ),
            329 => 
            array (
                'permission_id' => 174,
                'role_id' => 39,
            ),
            330 => 
            array (
                'permission_id' => 174,
                'role_id' => 40,
            ),
            331 => 
            array (
                'permission_id' => 174,
                'role_id' => 41,
            ),
            332 => 
            array (
                'permission_id' => 174,
                'role_id' => 42,
            ),
            333 => 
            array (
                'permission_id' => 174,
                'role_id' => 43,
            ),
            334 => 
            array (
                'permission_id' => 174,
                'role_id' => 44,
            ),
            335 => 
            array (
                'permission_id' => 174,
                'role_id' => 45,
            ),
            336 => 
            array (
                'permission_id' => 174,
                'role_id' => 46,
            ),
            337 => 
            array (
                'permission_id' => 174,
                'role_id' => 47,
            ),
            338 => 
            array (
                'permission_id' => 174,
                'role_id' => 48,
            ),
            339 => 
            array (
                'permission_id' => 175,
                'role_id' => 10,
            ),
            340 => 
            array (
                'permission_id' => 175,
                'role_id' => 35,
            ),
            341 => 
            array (
                'permission_id' => 175,
                'role_id' => 36,
            ),
            342 => 
            array (
                'permission_id' => 175,
                'role_id' => 37,
            ),
            343 => 
            array (
                'permission_id' => 175,
                'role_id' => 38,
            ),
            344 => 
            array (
                'permission_id' => 175,
                'role_id' => 39,
            ),
            345 => 
            array (
                'permission_id' => 175,
                'role_id' => 40,
            ),
            346 => 
            array (
                'permission_id' => 175,
                'role_id' => 41,
            ),
            347 => 
            array (
                'permission_id' => 175,
                'role_id' => 42,
            ),
            348 => 
            array (
                'permission_id' => 175,
                'role_id' => 43,
            ),
            349 => 
            array (
                'permission_id' => 175,
                'role_id' => 44,
            ),
            350 => 
            array (
                'permission_id' => 175,
                'role_id' => 45,
            ),
            351 => 
            array (
                'permission_id' => 175,
                'role_id' => 46,
            ),
            352 => 
            array (
                'permission_id' => 175,
                'role_id' => 47,
            ),
            353 => 
            array (
                'permission_id' => 175,
                'role_id' => 48,
            ),
            354 => 
            array (
                'permission_id' => 176,
                'role_id' => 10,
            ),
            355 => 
            array (
                'permission_id' => 176,
                'role_id' => 35,
            ),
            356 => 
            array (
                'permission_id' => 176,
                'role_id' => 37,
            ),
            357 => 
            array (
                'permission_id' => 176,
                'role_id' => 38,
            ),
            358 => 
            array (
                'permission_id' => 176,
                'role_id' => 39,
            ),
            359 => 
            array (
                'permission_id' => 176,
                'role_id' => 40,
            ),
            360 => 
            array (
                'permission_id' => 176,
                'role_id' => 41,
            ),
            361 => 
            array (
                'permission_id' => 176,
                'role_id' => 42,
            ),
            362 => 
            array (
                'permission_id' => 176,
                'role_id' => 43,
            ),
            363 => 
            array (
                'permission_id' => 176,
                'role_id' => 44,
            ),
            364 => 
            array (
                'permission_id' => 176,
                'role_id' => 45,
            ),
            365 => 
            array (
                'permission_id' => 176,
                'role_id' => 46,
            ),
            366 => 
            array (
                'permission_id' => 176,
                'role_id' => 47,
            ),
            367 => 
            array (
                'permission_id' => 176,
                'role_id' => 48,
            ),
            368 => 
            array (
                'permission_id' => 177,
                'role_id' => 10,
            ),
            369 => 
            array (
                'permission_id' => 177,
                'role_id' => 35,
            ),
            370 => 
            array (
                'permission_id' => 177,
                'role_id' => 37,
            ),
            371 => 
            array (
                'permission_id' => 177,
                'role_id' => 38,
            ),
            372 => 
            array (
                'permission_id' => 177,
                'role_id' => 39,
            ),
            373 => 
            array (
                'permission_id' => 177,
                'role_id' => 40,
            ),
            374 => 
            array (
                'permission_id' => 177,
                'role_id' => 41,
            ),
            375 => 
            array (
                'permission_id' => 177,
                'role_id' => 42,
            ),
            376 => 
            array (
                'permission_id' => 177,
                'role_id' => 43,
            ),
            377 => 
            array (
                'permission_id' => 177,
                'role_id' => 44,
            ),
            378 => 
            array (
                'permission_id' => 177,
                'role_id' => 45,
            ),
            379 => 
            array (
                'permission_id' => 177,
                'role_id' => 46,
            ),
            380 => 
            array (
                'permission_id' => 177,
                'role_id' => 47,
            ),
            381 => 
            array (
                'permission_id' => 177,
                'role_id' => 48,
            ),
            382 => 
            array (
                'permission_id' => 178,
                'role_id' => 10,
            ),
            383 => 
            array (
                'permission_id' => 178,
                'role_id' => 45,
            ),
            384 => 
            array (
                'permission_id' => 178,
                'role_id' => 46,
            ),
            385 => 
            array (
                'permission_id' => 178,
                'role_id' => 47,
            ),
            386 => 
            array (
                'permission_id' => 179,
                'role_id' => 10,
            ),
            387 => 
            array (
                'permission_id' => 179,
                'role_id' => 45,
            ),
            388 => 
            array (
                'permission_id' => 179,
                'role_id' => 47,
            ),
            389 => 
            array (
                'permission_id' => 180,
                'role_id' => 10,
            ),
            390 => 
            array (
                'permission_id' => 180,
                'role_id' => 45,
            ),
            391 => 
            array (
                'permission_id' => 180,
                'role_id' => 47,
            ),
            392 => 
            array (
                'permission_id' => 181,
                'role_id' => 10,
            ),
            393 => 
            array (
                'permission_id' => 181,
                'role_id' => 45,
            ),
            394 => 
            array (
                'permission_id' => 181,
                'role_id' => 47,
            ),
            395 => 
            array (
                'permission_id' => 182,
                'role_id' => 10,
            ),
            396 => 
            array (
                'permission_id' => 182,
                'role_id' => 35,
            ),
            397 => 
            array (
                'permission_id' => 182,
                'role_id' => 37,
            ),
            398 => 
            array (
                'permission_id' => 182,
                'role_id' => 38,
            ),
            399 => 
            array (
                'permission_id' => 182,
                'role_id' => 39,
            ),
            400 => 
            array (
                'permission_id' => 182,
                'role_id' => 40,
            ),
            401 => 
            array (
                'permission_id' => 182,
                'role_id' => 42,
            ),
            402 => 
            array (
                'permission_id' => 182,
                'role_id' => 43,
            ),
            403 => 
            array (
                'permission_id' => 182,
                'role_id' => 44,
            ),
            404 => 
            array (
                'permission_id' => 182,
                'role_id' => 45,
            ),
            405 => 
            array (
                'permission_id' => 182,
                'role_id' => 46,
            ),
            406 => 
            array (
                'permission_id' => 182,
                'role_id' => 47,
            ),
            407 => 
            array (
                'permission_id' => 182,
                'role_id' => 48,
            ),
            408 => 
            array (
                'permission_id' => 183,
                'role_id' => 10,
            ),
            409 => 
            array (
                'permission_id' => 183,
                'role_id' => 35,
            ),
            410 => 
            array (
                'permission_id' => 183,
                'role_id' => 36,
            ),
            411 => 
            array (
                'permission_id' => 183,
                'role_id' => 37,
            ),
            412 => 
            array (
                'permission_id' => 183,
                'role_id' => 38,
            ),
            413 => 
            array (
                'permission_id' => 183,
                'role_id' => 39,
            ),
            414 => 
            array (
                'permission_id' => 183,
                'role_id' => 40,
            ),
            415 => 
            array (
                'permission_id' => 183,
                'role_id' => 41,
            ),
            416 => 
            array (
                'permission_id' => 183,
                'role_id' => 42,
            ),
            417 => 
            array (
                'permission_id' => 183,
                'role_id' => 43,
            ),
            418 => 
            array (
                'permission_id' => 183,
                'role_id' => 44,
            ),
            419 => 
            array (
                'permission_id' => 183,
                'role_id' => 45,
            ),
            420 => 
            array (
                'permission_id' => 183,
                'role_id' => 46,
            ),
            421 => 
            array (
                'permission_id' => 183,
                'role_id' => 47,
            ),
            422 => 
            array (
                'permission_id' => 183,
                'role_id' => 48,
            ),
            423 => 
            array (
                'permission_id' => 184,
                'role_id' => 10,
            ),
            424 => 
            array (
                'permission_id' => 184,
                'role_id' => 35,
            ),
            425 => 
            array (
                'permission_id' => 184,
                'role_id' => 37,
            ),
            426 => 
            array (
                'permission_id' => 184,
                'role_id' => 38,
            ),
            427 => 
            array (
                'permission_id' => 184,
                'role_id' => 39,
            ),
            428 => 
            array (
                'permission_id' => 184,
                'role_id' => 40,
            ),
            429 => 
            array (
                'permission_id' => 184,
                'role_id' => 41,
            ),
            430 => 
            array (
                'permission_id' => 184,
                'role_id' => 42,
            ),
            431 => 
            array (
                'permission_id' => 184,
                'role_id' => 43,
            ),
            432 => 
            array (
                'permission_id' => 184,
                'role_id' => 44,
            ),
            433 => 
            array (
                'permission_id' => 184,
                'role_id' => 45,
            ),
            434 => 
            array (
                'permission_id' => 184,
                'role_id' => 46,
            ),
            435 => 
            array (
                'permission_id' => 184,
                'role_id' => 47,
            ),
            436 => 
            array (
                'permission_id' => 184,
                'role_id' => 48,
            ),
            437 => 
            array (
                'permission_id' => 185,
                'role_id' => 10,
            ),
            438 => 
            array (
                'permission_id' => 185,
                'role_id' => 35,
            ),
            439 => 
            array (
                'permission_id' => 185,
                'role_id' => 37,
            ),
            440 => 
            array (
                'permission_id' => 185,
                'role_id' => 38,
            ),
            441 => 
            array (
                'permission_id' => 185,
                'role_id' => 39,
            ),
            442 => 
            array (
                'permission_id' => 185,
                'role_id' => 40,
            ),
            443 => 
            array (
                'permission_id' => 185,
                'role_id' => 41,
            ),
            444 => 
            array (
                'permission_id' => 185,
                'role_id' => 42,
            ),
            445 => 
            array (
                'permission_id' => 185,
                'role_id' => 43,
            ),
            446 => 
            array (
                'permission_id' => 185,
                'role_id' => 44,
            ),
            447 => 
            array (
                'permission_id' => 185,
                'role_id' => 45,
            ),
            448 => 
            array (
                'permission_id' => 185,
                'role_id' => 46,
            ),
            449 => 
            array (
                'permission_id' => 185,
                'role_id' => 47,
            ),
            450 => 
            array (
                'permission_id' => 185,
                'role_id' => 48,
            ),
            451 => 
            array (
                'permission_id' => 186,
                'role_id' => 10,
            ),
            452 => 
            array (
                'permission_id' => 186,
                'role_id' => 35,
            ),
            453 => 
            array (
                'permission_id' => 186,
                'role_id' => 38,
            ),
            454 => 
            array (
                'permission_id' => 186,
                'role_id' => 40,
            ),
            455 => 
            array (
                'permission_id' => 186,
                'role_id' => 41,
            ),
            456 => 
            array (
                'permission_id' => 186,
                'role_id' => 42,
            ),
            457 => 
            array (
                'permission_id' => 186,
                'role_id' => 43,
            ),
            458 => 
            array (
                'permission_id' => 186,
                'role_id' => 47,
            ),
            459 => 
            array (
                'permission_id' => 186,
                'role_id' => 48,
            ),
            460 => 
            array (
                'permission_id' => 187,
                'role_id' => 10,
            ),
            461 => 
            array (
                'permission_id' => 187,
                'role_id' => 35,
            ),
            462 => 
            array (
                'permission_id' => 187,
                'role_id' => 42,
            ),
            463 => 
            array (
                'permission_id' => 187,
                'role_id' => 43,
            ),
            464 => 
            array (
                'permission_id' => 187,
                'role_id' => 47,
            ),
            465 => 
            array (
                'permission_id' => 187,
                'role_id' => 48,
            ),
            466 => 
            array (
                'permission_id' => 188,
                'role_id' => 10,
            ),
            467 => 
            array (
                'permission_id' => 188,
                'role_id' => 35,
            ),
            468 => 
            array (
                'permission_id' => 188,
                'role_id' => 42,
            ),
            469 => 
            array (
                'permission_id' => 188,
                'role_id' => 43,
            ),
            470 => 
            array (
                'permission_id' => 188,
                'role_id' => 47,
            ),
            471 => 
            array (
                'permission_id' => 188,
                'role_id' => 48,
            ),
            472 => 
            array (
                'permission_id' => 189,
                'role_id' => 10,
            ),
            473 => 
            array (
                'permission_id' => 189,
                'role_id' => 35,
            ),
            474 => 
            array (
                'permission_id' => 189,
                'role_id' => 42,
            ),
            475 => 
            array (
                'permission_id' => 189,
                'role_id' => 43,
            ),
            476 => 
            array (
                'permission_id' => 189,
                'role_id' => 47,
            ),
            477 => 
            array (
                'permission_id' => 189,
                'role_id' => 48,
            ),
            478 => 
            array (
                'permission_id' => 190,
                'role_id' => 10,
            ),
            479 => 
            array (
                'permission_id' => 190,
                'role_id' => 35,
            ),
            480 => 
            array (
                'permission_id' => 190,
                'role_id' => 42,
            ),
            481 => 
            array (
                'permission_id' => 190,
                'role_id' => 43,
            ),
            482 => 
            array (
                'permission_id' => 190,
                'role_id' => 47,
            ),
            483 => 
            array (
                'permission_id' => 190,
                'role_id' => 48,
            ),
            484 => 
            array (
                'permission_id' => 191,
                'role_id' => 10,
            ),
            485 => 
            array (
                'permission_id' => 191,
                'role_id' => 35,
            ),
            486 => 
            array (
                'permission_id' => 191,
                'role_id' => 43,
            ),
            487 => 
            array (
                'permission_id' => 191,
                'role_id' => 47,
            ),
            488 => 
            array (
                'permission_id' => 191,
                'role_id' => 48,
            ),
            489 => 
            array (
                'permission_id' => 192,
                'role_id' => 10,
            ),
            490 => 
            array (
                'permission_id' => 192,
                'role_id' => 43,
            ),
            491 => 
            array (
                'permission_id' => 192,
                'role_id' => 47,
            ),
            492 => 
            array (
                'permission_id' => 192,
                'role_id' => 48,
            ),
            493 => 
            array (
                'permission_id' => 193,
                'role_id' => 10,
            ),
            494 => 
            array (
                'permission_id' => 193,
                'role_id' => 43,
            ),
            495 => 
            array (
                'permission_id' => 193,
                'role_id' => 47,
            ),
            496 => 
            array (
                'permission_id' => 193,
                'role_id' => 48,
            ),
            497 => 
            array (
                'permission_id' => 194,
                'role_id' => 10,
            ),
            498 => 
            array (
                'permission_id' => 194,
                'role_id' => 43,
            ),
            499 => 
            array (
                'permission_id' => 194,
                'role_id' => 47,
            ),
        ));
        \DB::table('role_has_permissions')->insert(array (
            0 => 
            array (
                'permission_id' => 194,
                'role_id' => 48,
            ),
            1 => 
            array (
                'permission_id' => 195,
                'role_id' => 10,
            ),
            2 => 
            array (
                'permission_id' => 195,
                'role_id' => 43,
            ),
            3 => 
            array (
                'permission_id' => 195,
                'role_id' => 47,
            ),
            4 => 
            array (
                'permission_id' => 195,
                'role_id' => 48,
            ),
            5 => 
            array (
                'permission_id' => 196,
                'role_id' => 10,
            ),
            6 => 
            array (
                'permission_id' => 196,
                'role_id' => 43,
            ),
            7 => 
            array (
                'permission_id' => 196,
                'role_id' => 47,
            ),
            8 => 
            array (
                'permission_id' => 196,
                'role_id' => 48,
            ),
            9 => 
            array (
                'permission_id' => 197,
                'role_id' => 10,
            ),
            10 => 
            array (
                'permission_id' => 197,
                'role_id' => 35,
            ),
            11 => 
            array (
                'permission_id' => 197,
                'role_id' => 38,
            ),
            12 => 
            array (
                'permission_id' => 197,
                'role_id' => 40,
            ),
            13 => 
            array (
                'permission_id' => 197,
                'role_id' => 41,
            ),
            14 => 
            array (
                'permission_id' => 197,
                'role_id' => 42,
            ),
            15 => 
            array (
                'permission_id' => 197,
                'role_id' => 47,
            ),
            16 => 
            array (
                'permission_id' => 197,
                'role_id' => 48,
            ),
            17 => 
            array (
                'permission_id' => 198,
                'role_id' => 10,
            ),
            18 => 
            array (
                'permission_id' => 198,
                'role_id' => 35,
            ),
            19 => 
            array (
                'permission_id' => 198,
                'role_id' => 47,
            ),
            20 => 
            array (
                'permission_id' => 198,
                'role_id' => 48,
            ),
            21 => 
            array (
                'permission_id' => 199,
                'role_id' => 10,
            ),
            22 => 
            array (
                'permission_id' => 199,
                'role_id' => 35,
            ),
            23 => 
            array (
                'permission_id' => 199,
                'role_id' => 42,
            ),
            24 => 
            array (
                'permission_id' => 199,
                'role_id' => 47,
            ),
            25 => 
            array (
                'permission_id' => 199,
                'role_id' => 48,
            ),
            26 => 
            array (
                'permission_id' => 200,
                'role_id' => 10,
            ),
            27 => 
            array (
                'permission_id' => 200,
                'role_id' => 35,
            ),
            28 => 
            array (
                'permission_id' => 200,
                'role_id' => 42,
            ),
            29 => 
            array (
                'permission_id' => 200,
                'role_id' => 47,
            ),
            30 => 
            array (
                'permission_id' => 200,
                'role_id' => 48,
            ),
            31 => 
            array (
                'permission_id' => 201,
                'role_id' => 10,
            ),
            32 => 
            array (
                'permission_id' => 201,
                'role_id' => 35,
            ),
            33 => 
            array (
                'permission_id' => 201,
                'role_id' => 42,
            ),
            34 => 
            array (
                'permission_id' => 201,
                'role_id' => 47,
            ),
            35 => 
            array (
                'permission_id' => 201,
                'role_id' => 48,
            ),
            36 => 
            array (
                'permission_id' => 202,
                'role_id' => 10,
            ),
            37 => 
            array (
                'permission_id' => 202,
                'role_id' => 35,
            ),
            38 => 
            array (
                'permission_id' => 202,
                'role_id' => 47,
            ),
            39 => 
            array (
                'permission_id' => 202,
                'role_id' => 48,
            ),
            40 => 
            array (
                'permission_id' => 203,
                'role_id' => 10,
            ),
            41 => 
            array (
                'permission_id' => 203,
                'role_id' => 35,
            ),
            42 => 
            array (
                'permission_id' => 203,
                'role_id' => 47,
            ),
            43 => 
            array (
                'permission_id' => 203,
                'role_id' => 48,
            ),
            44 => 
            array (
                'permission_id' => 204,
                'role_id' => 10,
            ),
            45 => 
            array (
                'permission_id' => 204,
                'role_id' => 35,
            ),
            46 => 
            array (
                'permission_id' => 204,
                'role_id' => 47,
            ),
            47 => 
            array (
                'permission_id' => 204,
                'role_id' => 48,
            ),
            48 => 
            array (
                'permission_id' => 205,
                'role_id' => 10,
            ),
            49 => 
            array (
                'permission_id' => 205,
                'role_id' => 35,
            ),
            50 => 
            array (
                'permission_id' => 205,
                'role_id' => 47,
            ),
            51 => 
            array (
                'permission_id' => 205,
                'role_id' => 48,
            ),
            52 => 
            array (
                'permission_id' => 206,
                'role_id' => 10,
            ),
            53 => 
            array (
                'permission_id' => 206,
                'role_id' => 35,
            ),
            54 => 
            array (
                'permission_id' => 206,
                'role_id' => 47,
            ),
            55 => 
            array (
                'permission_id' => 206,
                'role_id' => 48,
            ),
            56 => 
            array (
                'permission_id' => 207,
                'role_id' => 10,
            ),
            57 => 
            array (
                'permission_id' => 207,
                'role_id' => 35,
            ),
            58 => 
            array (
                'permission_id' => 207,
                'role_id' => 47,
            ),
            59 => 
            array (
                'permission_id' => 207,
                'role_id' => 48,
            ),
            60 => 
            array (
                'permission_id' => 208,
                'role_id' => 10,
            ),
            61 => 
            array (
                'permission_id' => 208,
                'role_id' => 35,
            ),
            62 => 
            array (
                'permission_id' => 208,
                'role_id' => 38,
            ),
            63 => 
            array (
                'permission_id' => 208,
                'role_id' => 40,
            ),
            64 => 
            array (
                'permission_id' => 208,
                'role_id' => 41,
            ),
            65 => 
            array (
                'permission_id' => 208,
                'role_id' => 42,
            ),
            66 => 
            array (
                'permission_id' => 208,
                'role_id' => 44,
            ),
            67 => 
            array (
                'permission_id' => 208,
                'role_id' => 47,
            ),
            68 => 
            array (
                'permission_id' => 208,
                'role_id' => 48,
            ),
            69 => 
            array (
                'permission_id' => 209,
                'role_id' => 10,
            ),
            70 => 
            array (
                'permission_id' => 209,
                'role_id' => 35,
            ),
            71 => 
            array (
                'permission_id' => 209,
                'role_id' => 42,
            ),
            72 => 
            array (
                'permission_id' => 209,
                'role_id' => 47,
            ),
            73 => 
            array (
                'permission_id' => 209,
                'role_id' => 48,
            ),
            74 => 
            array (
                'permission_id' => 210,
                'role_id' => 10,
            ),
            75 => 
            array (
                'permission_id' => 210,
                'role_id' => 35,
            ),
            76 => 
            array (
                'permission_id' => 210,
                'role_id' => 42,
            ),
            77 => 
            array (
                'permission_id' => 210,
                'role_id' => 47,
            ),
            78 => 
            array (
                'permission_id' => 210,
                'role_id' => 48,
            ),
            79 => 
            array (
                'permission_id' => 211,
                'role_id' => 10,
            ),
            80 => 
            array (
                'permission_id' => 211,
                'role_id' => 35,
            ),
            81 => 
            array (
                'permission_id' => 211,
                'role_id' => 42,
            ),
            82 => 
            array (
                'permission_id' => 211,
                'role_id' => 47,
            ),
            83 => 
            array (
                'permission_id' => 211,
                'role_id' => 48,
            ),
            84 => 
            array (
                'permission_id' => 212,
                'role_id' => 10,
            ),
            85 => 
            array (
                'permission_id' => 212,
                'role_id' => 35,
            ),
            86 => 
            array (
                'permission_id' => 212,
                'role_id' => 42,
            ),
            87 => 
            array (
                'permission_id' => 212,
                'role_id' => 47,
            ),
            88 => 
            array (
                'permission_id' => 212,
                'role_id' => 48,
            ),
            89 => 
            array (
                'permission_id' => 213,
                'role_id' => 10,
            ),
            90 => 
            array (
                'permission_id' => 213,
                'role_id' => 35,
            ),
            91 => 
            array (
                'permission_id' => 213,
                'role_id' => 47,
            ),
            92 => 
            array (
                'permission_id' => 213,
                'role_id' => 48,
            ),
            93 => 
            array (
                'permission_id' => 214,
                'role_id' => 10,
            ),
            94 => 
            array (
                'permission_id' => 214,
                'role_id' => 35,
            ),
            95 => 
            array (
                'permission_id' => 214,
                'role_id' => 47,
            ),
            96 => 
            array (
                'permission_id' => 214,
                'role_id' => 48,
            ),
            97 => 
            array (
                'permission_id' => 215,
                'role_id' => 10,
            ),
            98 => 
            array (
                'permission_id' => 215,
                'role_id' => 35,
            ),
            99 => 
            array (
                'permission_id' => 215,
                'role_id' => 47,
            ),
            100 => 
            array (
                'permission_id' => 215,
                'role_id' => 48,
            ),
            101 => 
            array (
                'permission_id' => 216,
                'role_id' => 10,
            ),
            102 => 
            array (
                'permission_id' => 216,
                'role_id' => 35,
            ),
            103 => 
            array (
                'permission_id' => 216,
                'role_id' => 47,
            ),
            104 => 
            array (
                'permission_id' => 216,
                'role_id' => 48,
            ),
            105 => 
            array (
                'permission_id' => 217,
                'role_id' => 10,
            ),
            106 => 
            array (
                'permission_id' => 217,
                'role_id' => 35,
            ),
            107 => 
            array (
                'permission_id' => 217,
                'role_id' => 47,
            ),
            108 => 
            array (
                'permission_id' => 217,
                'role_id' => 48,
            ),
            109 => 
            array (
                'permission_id' => 218,
                'role_id' => 10,
            ),
            110 => 
            array (
                'permission_id' => 218,
                'role_id' => 35,
            ),
            111 => 
            array (
                'permission_id' => 218,
                'role_id' => 47,
            ),
            112 => 
            array (
                'permission_id' => 218,
                'role_id' => 48,
            ),
            113 => 
            array (
                'permission_id' => 219,
                'role_id' => 10,
            ),
            114 => 
            array (
                'permission_id' => 219,
                'role_id' => 35,
            ),
            115 => 
            array (
                'permission_id' => 219,
                'role_id' => 38,
            ),
            116 => 
            array (
                'permission_id' => 219,
                'role_id' => 40,
            ),
            117 => 
            array (
                'permission_id' => 219,
                'role_id' => 41,
            ),
            118 => 
            array (
                'permission_id' => 219,
                'role_id' => 42,
            ),
            119 => 
            array (
                'permission_id' => 219,
                'role_id' => 43,
            ),
            120 => 
            array (
                'permission_id' => 219,
                'role_id' => 44,
            ),
            121 => 
            array (
                'permission_id' => 219,
                'role_id' => 47,
            ),
            122 => 
            array (
                'permission_id' => 219,
                'role_id' => 48,
            ),
            123 => 
            array (
                'permission_id' => 220,
                'role_id' => 10,
            ),
            124 => 
            array (
                'permission_id' => 220,
                'role_id' => 42,
            ),
            125 => 
            array (
                'permission_id' => 220,
                'role_id' => 43,
            ),
            126 => 
            array (
                'permission_id' => 220,
                'role_id' => 44,
            ),
            127 => 
            array (
                'permission_id' => 220,
                'role_id' => 47,
            ),
            128 => 
            array (
                'permission_id' => 220,
                'role_id' => 48,
            ),
            129 => 
            array (
                'permission_id' => 221,
                'role_id' => 10,
            ),
            130 => 
            array (
                'permission_id' => 221,
                'role_id' => 35,
            ),
            131 => 
            array (
                'permission_id' => 221,
                'role_id' => 42,
            ),
            132 => 
            array (
                'permission_id' => 221,
                'role_id' => 43,
            ),
            133 => 
            array (
                'permission_id' => 221,
                'role_id' => 44,
            ),
            134 => 
            array (
                'permission_id' => 221,
                'role_id' => 47,
            ),
            135 => 
            array (
                'permission_id' => 221,
                'role_id' => 48,
            ),
            136 => 
            array (
                'permission_id' => 222,
                'role_id' => 10,
            ),
            137 => 
            array (
                'permission_id' => 222,
                'role_id' => 35,
            ),
            138 => 
            array (
                'permission_id' => 222,
                'role_id' => 42,
            ),
            139 => 
            array (
                'permission_id' => 222,
                'role_id' => 43,
            ),
            140 => 
            array (
                'permission_id' => 222,
                'role_id' => 44,
            ),
            141 => 
            array (
                'permission_id' => 222,
                'role_id' => 47,
            ),
            142 => 
            array (
                'permission_id' => 222,
                'role_id' => 48,
            ),
            143 => 
            array (
                'permission_id' => 223,
                'role_id' => 10,
            ),
            144 => 
            array (
                'permission_id' => 223,
                'role_id' => 35,
            ),
            145 => 
            array (
                'permission_id' => 223,
                'role_id' => 42,
            ),
            146 => 
            array (
                'permission_id' => 223,
                'role_id' => 43,
            ),
            147 => 
            array (
                'permission_id' => 223,
                'role_id' => 44,
            ),
            148 => 
            array (
                'permission_id' => 223,
                'role_id' => 47,
            ),
            149 => 
            array (
                'permission_id' => 223,
                'role_id' => 48,
            ),
            150 => 
            array (
                'permission_id' => 224,
                'role_id' => 10,
            ),
            151 => 
            array (
                'permission_id' => 224,
                'role_id' => 35,
            ),
            152 => 
            array (
                'permission_id' => 224,
                'role_id' => 43,
            ),
            153 => 
            array (
                'permission_id' => 224,
                'role_id' => 44,
            ),
            154 => 
            array (
                'permission_id' => 224,
                'role_id' => 47,
            ),
            155 => 
            array (
                'permission_id' => 224,
                'role_id' => 48,
            ),
            156 => 
            array (
                'permission_id' => 225,
                'role_id' => 10,
            ),
            157 => 
            array (
                'permission_id' => 225,
                'role_id' => 43,
            ),
            158 => 
            array (
                'permission_id' => 225,
                'role_id' => 44,
            ),
            159 => 
            array (
                'permission_id' => 225,
                'role_id' => 47,
            ),
            160 => 
            array (
                'permission_id' => 225,
                'role_id' => 48,
            ),
            161 => 
            array (
                'permission_id' => 226,
                'role_id' => 10,
            ),
            162 => 
            array (
                'permission_id' => 226,
                'role_id' => 43,
            ),
            163 => 
            array (
                'permission_id' => 226,
                'role_id' => 44,
            ),
            164 => 
            array (
                'permission_id' => 226,
                'role_id' => 47,
            ),
            165 => 
            array (
                'permission_id' => 226,
                'role_id' => 48,
            ),
            166 => 
            array (
                'permission_id' => 227,
                'role_id' => 10,
            ),
            167 => 
            array (
                'permission_id' => 227,
                'role_id' => 43,
            ),
            168 => 
            array (
                'permission_id' => 227,
                'role_id' => 44,
            ),
            169 => 
            array (
                'permission_id' => 227,
                'role_id' => 47,
            ),
            170 => 
            array (
                'permission_id' => 227,
                'role_id' => 48,
            ),
            171 => 
            array (
                'permission_id' => 228,
                'role_id' => 10,
            ),
            172 => 
            array (
                'permission_id' => 228,
                'role_id' => 43,
            ),
            173 => 
            array (
                'permission_id' => 228,
                'role_id' => 44,
            ),
            174 => 
            array (
                'permission_id' => 228,
                'role_id' => 47,
            ),
            175 => 
            array (
                'permission_id' => 228,
                'role_id' => 48,
            ),
            176 => 
            array (
                'permission_id' => 229,
                'role_id' => 10,
            ),
            177 => 
            array (
                'permission_id' => 229,
                'role_id' => 43,
            ),
            178 => 
            array (
                'permission_id' => 229,
                'role_id' => 44,
            ),
            179 => 
            array (
                'permission_id' => 229,
                'role_id' => 47,
            ),
            180 => 
            array (
                'permission_id' => 229,
                'role_id' => 48,
            ),
            181 => 
            array (
                'permission_id' => 230,
                'role_id' => 10,
            ),
            182 => 
            array (
                'permission_id' => 230,
                'role_id' => 35,
            ),
            183 => 
            array (
                'permission_id' => 230,
                'role_id' => 38,
            ),
            184 => 
            array (
                'permission_id' => 230,
                'role_id' => 40,
            ),
            185 => 
            array (
                'permission_id' => 230,
                'role_id' => 41,
            ),
            186 => 
            array (
                'permission_id' => 230,
                'role_id' => 42,
            ),
            187 => 
            array (
                'permission_id' => 230,
                'role_id' => 43,
            ),
            188 => 
            array (
                'permission_id' => 230,
                'role_id' => 44,
            ),
            189 => 
            array (
                'permission_id' => 230,
                'role_id' => 47,
            ),
            190 => 
            array (
                'permission_id' => 230,
                'role_id' => 48,
            ),
            191 => 
            array (
                'permission_id' => 231,
                'role_id' => 10,
            ),
            192 => 
            array (
                'permission_id' => 231,
                'role_id' => 35,
            ),
            193 => 
            array (
                'permission_id' => 231,
                'role_id' => 42,
            ),
            194 => 
            array (
                'permission_id' => 231,
                'role_id' => 43,
            ),
            195 => 
            array (
                'permission_id' => 231,
                'role_id' => 44,
            ),
            196 => 
            array (
                'permission_id' => 231,
                'role_id' => 47,
            ),
            197 => 
            array (
                'permission_id' => 231,
                'role_id' => 48,
            ),
            198 => 
            array (
                'permission_id' => 232,
                'role_id' => 10,
            ),
            199 => 
            array (
                'permission_id' => 232,
                'role_id' => 35,
            ),
            200 => 
            array (
                'permission_id' => 232,
                'role_id' => 42,
            ),
            201 => 
            array (
                'permission_id' => 232,
                'role_id' => 43,
            ),
            202 => 
            array (
                'permission_id' => 232,
                'role_id' => 44,
            ),
            203 => 
            array (
                'permission_id' => 232,
                'role_id' => 47,
            ),
            204 => 
            array (
                'permission_id' => 232,
                'role_id' => 48,
            ),
            205 => 
            array (
                'permission_id' => 233,
                'role_id' => 10,
            ),
            206 => 
            array (
                'permission_id' => 233,
                'role_id' => 35,
            ),
            207 => 
            array (
                'permission_id' => 233,
                'role_id' => 42,
            ),
            208 => 
            array (
                'permission_id' => 233,
                'role_id' => 43,
            ),
            209 => 
            array (
                'permission_id' => 233,
                'role_id' => 47,
            ),
            210 => 
            array (
                'permission_id' => 234,
                'role_id' => 10,
            ),
            211 => 
            array (
                'permission_id' => 234,
                'role_id' => 35,
            ),
            212 => 
            array (
                'permission_id' => 234,
                'role_id' => 43,
            ),
            213 => 
            array (
                'permission_id' => 234,
                'role_id' => 47,
            ),
            214 => 
            array (
                'permission_id' => 234,
                'role_id' => 48,
            ),
            215 => 
            array (
                'permission_id' => 235,
                'role_id' => 10,
            ),
            216 => 
            array (
                'permission_id' => 235,
                'role_id' => 43,
            ),
            217 => 
            array (
                'permission_id' => 235,
                'role_id' => 44,
            ),
            218 => 
            array (
                'permission_id' => 235,
                'role_id' => 47,
            ),
            219 => 
            array (
                'permission_id' => 235,
                'role_id' => 48,
            ),
            220 => 
            array (
                'permission_id' => 236,
                'role_id' => 10,
            ),
            221 => 
            array (
                'permission_id' => 236,
                'role_id' => 43,
            ),
            222 => 
            array (
                'permission_id' => 236,
                'role_id' => 47,
            ),
            223 => 
            array (
                'permission_id' => 237,
                'role_id' => 10,
            ),
            224 => 
            array (
                'permission_id' => 237,
                'role_id' => 43,
            ),
            225 => 
            array (
                'permission_id' => 237,
                'role_id' => 47,
            ),
            226 => 
            array (
                'permission_id' => 238,
                'role_id' => 10,
            ),
            227 => 
            array (
                'permission_id' => 238,
                'role_id' => 43,
            ),
            228 => 
            array (
                'permission_id' => 238,
                'role_id' => 47,
            ),
            229 => 
            array (
                'permission_id' => 239,
                'role_id' => 10,
            ),
            230 => 
            array (
                'permission_id' => 239,
                'role_id' => 43,
            ),
            231 => 
            array (
                'permission_id' => 239,
                'role_id' => 47,
            ),
            232 => 
            array (
                'permission_id' => 240,
                'role_id' => 10,
            ),
            233 => 
            array (
                'permission_id' => 240,
                'role_id' => 43,
            ),
            234 => 
            array (
                'permission_id' => 240,
                'role_id' => 44,
            ),
            235 => 
            array (
                'permission_id' => 240,
                'role_id' => 47,
            ),
            236 => 
            array (
                'permission_id' => 240,
                'role_id' => 48,
            ),
            237 => 
            array (
                'permission_id' => 241,
                'role_id' => 10,
            ),
            238 => 
            array (
                'permission_id' => 241,
                'role_id' => 38,
            ),
            239 => 
            array (
                'permission_id' => 241,
                'role_id' => 40,
            ),
            240 => 
            array (
                'permission_id' => 241,
                'role_id' => 41,
            ),
            241 => 
            array (
                'permission_id' => 241,
                'role_id' => 42,
            ),
            242 => 
            array (
                'permission_id' => 241,
                'role_id' => 43,
            ),
            243 => 
            array (
                'permission_id' => 241,
                'role_id' => 44,
            ),
            244 => 
            array (
                'permission_id' => 241,
                'role_id' => 47,
            ),
            245 => 
            array (
                'permission_id' => 241,
                'role_id' => 48,
            ),
            246 => 
            array (
                'permission_id' => 242,
                'role_id' => 10,
            ),
            247 => 
            array (
                'permission_id' => 242,
                'role_id' => 42,
            ),
            248 => 
            array (
                'permission_id' => 242,
                'role_id' => 43,
            ),
            249 => 
            array (
                'permission_id' => 242,
                'role_id' => 44,
            ),
            250 => 
            array (
                'permission_id' => 242,
                'role_id' => 47,
            ),
            251 => 
            array (
                'permission_id' => 242,
                'role_id' => 48,
            ),
            252 => 
            array (
                'permission_id' => 243,
                'role_id' => 10,
            ),
            253 => 
            array (
                'permission_id' => 243,
                'role_id' => 42,
            ),
            254 => 
            array (
                'permission_id' => 243,
                'role_id' => 43,
            ),
            255 => 
            array (
                'permission_id' => 243,
                'role_id' => 44,
            ),
            256 => 
            array (
                'permission_id' => 243,
                'role_id' => 47,
            ),
            257 => 
            array (
                'permission_id' => 243,
                'role_id' => 48,
            ),
            258 => 
            array (
                'permission_id' => 244,
                'role_id' => 10,
            ),
            259 => 
            array (
                'permission_id' => 244,
                'role_id' => 42,
            ),
            260 => 
            array (
                'permission_id' => 244,
                'role_id' => 43,
            ),
            261 => 
            array (
                'permission_id' => 244,
                'role_id' => 47,
            ),
            262 => 
            array (
                'permission_id' => 245,
                'role_id' => 10,
            ),
            263 => 
            array (
                'permission_id' => 245,
                'role_id' => 43,
            ),
            264 => 
            array (
                'permission_id' => 245,
                'role_id' => 47,
            ),
            265 => 
            array (
                'permission_id' => 245,
                'role_id' => 48,
            ),
            266 => 
            array (
                'permission_id' => 246,
                'role_id' => 10,
            ),
            267 => 
            array (
                'permission_id' => 246,
                'role_id' => 43,
            ),
            268 => 
            array (
                'permission_id' => 246,
                'role_id' => 44,
            ),
            269 => 
            array (
                'permission_id' => 246,
                'role_id' => 47,
            ),
            270 => 
            array (
                'permission_id' => 246,
                'role_id' => 48,
            ),
            271 => 
            array (
                'permission_id' => 247,
                'role_id' => 10,
            ),
            272 => 
            array (
                'permission_id' => 247,
                'role_id' => 43,
            ),
            273 => 
            array (
                'permission_id' => 247,
                'role_id' => 47,
            ),
            274 => 
            array (
                'permission_id' => 248,
                'role_id' => 10,
            ),
            275 => 
            array (
                'permission_id' => 248,
                'role_id' => 43,
            ),
            276 => 
            array (
                'permission_id' => 248,
                'role_id' => 47,
            ),
            277 => 
            array (
                'permission_id' => 249,
                'role_id' => 10,
            ),
            278 => 
            array (
                'permission_id' => 249,
                'role_id' => 43,
            ),
            279 => 
            array (
                'permission_id' => 249,
                'role_id' => 47,
            ),
            280 => 
            array (
                'permission_id' => 250,
                'role_id' => 10,
            ),
            281 => 
            array (
                'permission_id' => 250,
                'role_id' => 43,
            ),
            282 => 
            array (
                'permission_id' => 250,
                'role_id' => 47,
            ),
            283 => 
            array (
                'permission_id' => 251,
                'role_id' => 10,
            ),
            284 => 
            array (
                'permission_id' => 251,
                'role_id' => 35,
            ),
            285 => 
            array (
                'permission_id' => 251,
                'role_id' => 38,
            ),
            286 => 
            array (
                'permission_id' => 251,
                'role_id' => 40,
            ),
            287 => 
            array (
                'permission_id' => 251,
                'role_id' => 41,
            ),
            288 => 
            array (
                'permission_id' => 251,
                'role_id' => 42,
            ),
            289 => 
            array (
                'permission_id' => 251,
                'role_id' => 47,
            ),
            290 => 
            array (
                'permission_id' => 252,
                'role_id' => 10,
            ),
            291 => 
            array (
                'permission_id' => 252,
                'role_id' => 35,
            ),
            292 => 
            array (
                'permission_id' => 252,
                'role_id' => 42,
            ),
            293 => 
            array (
                'permission_id' => 252,
                'role_id' => 47,
            ),
            294 => 
            array (
                'permission_id' => 253,
                'role_id' => 10,
            ),
            295 => 
            array (
                'permission_id' => 253,
                'role_id' => 35,
            ),
            296 => 
            array (
                'permission_id' => 253,
                'role_id' => 42,
            ),
            297 => 
            array (
                'permission_id' => 253,
                'role_id' => 47,
            ),
            298 => 
            array (
                'permission_id' => 254,
                'role_id' => 10,
            ),
            299 => 
            array (
                'permission_id' => 254,
                'role_id' => 35,
            ),
            300 => 
            array (
                'permission_id' => 254,
                'role_id' => 42,
            ),
            301 => 
            array (
                'permission_id' => 254,
                'role_id' => 47,
            ),
            302 => 
            array (
                'permission_id' => 255,
                'role_id' => 10,
            ),
            303 => 
            array (
                'permission_id' => 255,
                'role_id' => 35,
            ),
            304 => 
            array (
                'permission_id' => 255,
                'role_id' => 47,
            ),
            305 => 
            array (
                'permission_id' => 256,
                'role_id' => 10,
            ),
            306 => 
            array (
                'permission_id' => 256,
                'role_id' => 35,
            ),
            307 => 
            array (
                'permission_id' => 256,
                'role_id' => 47,
            ),
            308 => 
            array (
                'permission_id' => 257,
                'role_id' => 10,
            ),
            309 => 
            array (
                'permission_id' => 257,
                'role_id' => 35,
            ),
            310 => 
            array (
                'permission_id' => 257,
                'role_id' => 47,
            ),
            311 => 
            array (
                'permission_id' => 258,
                'role_id' => 10,
            ),
            312 => 
            array (
                'permission_id' => 258,
                'role_id' => 35,
            ),
            313 => 
            array (
                'permission_id' => 258,
                'role_id' => 47,
            ),
            314 => 
            array (
                'permission_id' => 259,
                'role_id' => 10,
            ),
            315 => 
            array (
                'permission_id' => 259,
                'role_id' => 35,
            ),
            316 => 
            array (
                'permission_id' => 259,
                'role_id' => 47,
            ),
            317 => 
            array (
                'permission_id' => 260,
                'role_id' => 10,
            ),
            318 => 
            array (
                'permission_id' => 260,
                'role_id' => 35,
            ),
            319 => 
            array (
                'permission_id' => 260,
                'role_id' => 47,
            ),
            320 => 
            array (
                'permission_id' => 261,
                'role_id' => 10,
            ),
            321 => 
            array (
                'permission_id' => 261,
                'role_id' => 35,
            ),
            322 => 
            array (
                'permission_id' => 261,
                'role_id' => 38,
            ),
            323 => 
            array (
                'permission_id' => 261,
                'role_id' => 40,
            ),
            324 => 
            array (
                'permission_id' => 261,
                'role_id' => 41,
            ),
            325 => 
            array (
                'permission_id' => 261,
                'role_id' => 42,
            ),
            326 => 
            array (
                'permission_id' => 261,
                'role_id' => 44,
            ),
            327 => 
            array (
                'permission_id' => 261,
                'role_id' => 47,
            ),
            328 => 
            array (
                'permission_id' => 261,
                'role_id' => 48,
            ),
            329 => 
            array (
                'permission_id' => 262,
                'role_id' => 10,
            ),
            330 => 
            array (
                'permission_id' => 262,
                'role_id' => 35,
            ),
            331 => 
            array (
                'permission_id' => 262,
                'role_id' => 42,
            ),
            332 => 
            array (
                'permission_id' => 262,
                'role_id' => 44,
            ),
            333 => 
            array (
                'permission_id' => 262,
                'role_id' => 47,
            ),
            334 => 
            array (
                'permission_id' => 262,
                'role_id' => 48,
            ),
            335 => 
            array (
                'permission_id' => 263,
                'role_id' => 10,
            ),
            336 => 
            array (
                'permission_id' => 263,
                'role_id' => 35,
            ),
            337 => 
            array (
                'permission_id' => 263,
                'role_id' => 42,
            ),
            338 => 
            array (
                'permission_id' => 263,
                'role_id' => 44,
            ),
            339 => 
            array (
                'permission_id' => 263,
                'role_id' => 47,
            ),
            340 => 
            array (
                'permission_id' => 263,
                'role_id' => 48,
            ),
            341 => 
            array (
                'permission_id' => 264,
                'role_id' => 10,
            ),
            342 => 
            array (
                'permission_id' => 264,
                'role_id' => 35,
            ),
            343 => 
            array (
                'permission_id' => 264,
                'role_id' => 42,
            ),
            344 => 
            array (
                'permission_id' => 264,
                'role_id' => 44,
            ),
            345 => 
            array (
                'permission_id' => 264,
                'role_id' => 47,
            ),
            346 => 
            array (
                'permission_id' => 264,
                'role_id' => 48,
            ),
            347 => 
            array (
                'permission_id' => 265,
                'role_id' => 10,
            ),
            348 => 
            array (
                'permission_id' => 265,
                'role_id' => 35,
            ),
            349 => 
            array (
                'permission_id' => 265,
                'role_id' => 47,
            ),
            350 => 
            array (
                'permission_id' => 265,
                'role_id' => 48,
            ),
            351 => 
            array (
                'permission_id' => 266,
                'role_id' => 10,
            ),
            352 => 
            array (
                'permission_id' => 266,
                'role_id' => 35,
            ),
            353 => 
            array (
                'permission_id' => 266,
                'role_id' => 47,
            ),
            354 => 
            array (
                'permission_id' => 267,
                'role_id' => 10,
            ),
            355 => 
            array (
                'permission_id' => 267,
                'role_id' => 35,
            ),
            356 => 
            array (
                'permission_id' => 267,
                'role_id' => 47,
            ),
            357 => 
            array (
                'permission_id' => 268,
                'role_id' => 10,
            ),
            358 => 
            array (
                'permission_id' => 268,
                'role_id' => 35,
            ),
            359 => 
            array (
                'permission_id' => 268,
                'role_id' => 47,
            ),
            360 => 
            array (
                'permission_id' => 269,
                'role_id' => 10,
            ),
            361 => 
            array (
                'permission_id' => 269,
                'role_id' => 35,
            ),
            362 => 
            array (
                'permission_id' => 269,
                'role_id' => 47,
            ),
            363 => 
            array (
                'permission_id' => 270,
                'role_id' => 10,
            ),
            364 => 
            array (
                'permission_id' => 270,
                'role_id' => 35,
            ),
            365 => 
            array (
                'permission_id' => 270,
                'role_id' => 47,
            ),
            366 => 
            array (
                'permission_id' => 271,
                'role_id' => 10,
            ),
            367 => 
            array (
                'permission_id' => 271,
                'role_id' => 35,
            ),
            368 => 
            array (
                'permission_id' => 271,
                'role_id' => 38,
            ),
            369 => 
            array (
                'permission_id' => 271,
                'role_id' => 40,
            ),
            370 => 
            array (
                'permission_id' => 271,
                'role_id' => 41,
            ),
            371 => 
            array (
                'permission_id' => 271,
                'role_id' => 42,
            ),
            372 => 
            array (
                'permission_id' => 271,
                'role_id' => 43,
            ),
            373 => 
            array (
                'permission_id' => 271,
                'role_id' => 47,
            ),
            374 => 
            array (
                'permission_id' => 272,
                'role_id' => 10,
            ),
            375 => 
            array (
                'permission_id' => 272,
                'role_id' => 35,
            ),
            376 => 
            array (
                'permission_id' => 272,
                'role_id' => 42,
            ),
            377 => 
            array (
                'permission_id' => 272,
                'role_id' => 43,
            ),
            378 => 
            array (
                'permission_id' => 272,
                'role_id' => 47,
            ),
            379 => 
            array (
                'permission_id' => 273,
                'role_id' => 10,
            ),
            380 => 
            array (
                'permission_id' => 273,
                'role_id' => 35,
            ),
            381 => 
            array (
                'permission_id' => 273,
                'role_id' => 42,
            ),
            382 => 
            array (
                'permission_id' => 273,
                'role_id' => 43,
            ),
            383 => 
            array (
                'permission_id' => 273,
                'role_id' => 47,
            ),
            384 => 
            array (
                'permission_id' => 274,
                'role_id' => 10,
            ),
            385 => 
            array (
                'permission_id' => 274,
                'role_id' => 35,
            ),
            386 => 
            array (
                'permission_id' => 274,
                'role_id' => 43,
            ),
            387 => 
            array (
                'permission_id' => 274,
                'role_id' => 47,
            ),
            388 => 
            array (
                'permission_id' => 275,
                'role_id' => 10,
            ),
            389 => 
            array (
                'permission_id' => 275,
                'role_id' => 35,
            ),
            390 => 
            array (
                'permission_id' => 275,
                'role_id' => 38,
            ),
            391 => 
            array (
                'permission_id' => 275,
                'role_id' => 41,
            ),
            392 => 
            array (
                'permission_id' => 275,
                'role_id' => 42,
            ),
            393 => 
            array (
                'permission_id' => 275,
                'role_id' => 43,
            ),
            394 => 
            array (
                'permission_id' => 275,
                'role_id' => 47,
            ),
            395 => 
            array (
                'permission_id' => 276,
                'role_id' => 10,
            ),
            396 => 
            array (
                'permission_id' => 276,
                'role_id' => 35,
            ),
            397 => 
            array (
                'permission_id' => 276,
                'role_id' => 42,
            ),
            398 => 
            array (
                'permission_id' => 276,
                'role_id' => 43,
            ),
            399 => 
            array (
                'permission_id' => 276,
                'role_id' => 47,
            ),
            400 => 
            array (
                'permission_id' => 277,
                'role_id' => 10,
            ),
            401 => 
            array (
                'permission_id' => 277,
                'role_id' => 35,
            ),
            402 => 
            array (
                'permission_id' => 277,
                'role_id' => 42,
            ),
            403 => 
            array (
                'permission_id' => 277,
                'role_id' => 43,
            ),
            404 => 
            array (
                'permission_id' => 277,
                'role_id' => 47,
            ),
            405 => 
            array (
                'permission_id' => 278,
                'role_id' => 10,
            ),
            406 => 
            array (
                'permission_id' => 278,
                'role_id' => 35,
            ),
            407 => 
            array (
                'permission_id' => 278,
                'role_id' => 43,
            ),
            408 => 
            array (
                'permission_id' => 278,
                'role_id' => 47,
            ),
            409 => 
            array (
                'permission_id' => 279,
                'role_id' => 10,
            ),
            410 => 
            array (
                'permission_id' => 279,
                'role_id' => 35,
            ),
            411 => 
            array (
                'permission_id' => 279,
                'role_id' => 38,
            ),
            412 => 
            array (
                'permission_id' => 279,
                'role_id' => 47,
            ),
            413 => 
            array (
                'permission_id' => 280,
                'role_id' => 10,
            ),
            414 => 
            array (
                'permission_id' => 280,
                'role_id' => 47,
            ),
            415 => 
            array (
                'permission_id' => 281,
                'role_id' => 10,
            ),
            416 => 
            array (
                'permission_id' => 281,
                'role_id' => 47,
            ),
            417 => 
            array (
                'permission_id' => 282,
                'role_id' => 10,
            ),
            418 => 
            array (
                'permission_id' => 282,
                'role_id' => 47,
            ),
            419 => 
            array (
                'permission_id' => 283,
                'role_id' => 10,
            ),
            420 => 
            array (
                'permission_id' => 283,
                'role_id' => 35,
            ),
            421 => 
            array (
                'permission_id' => 283,
                'role_id' => 38,
            ),
            422 => 
            array (
                'permission_id' => 283,
                'role_id' => 40,
            ),
            423 => 
            array (
                'permission_id' => 283,
                'role_id' => 41,
            ),
            424 => 
            array (
                'permission_id' => 283,
                'role_id' => 42,
            ),
            425 => 
            array (
                'permission_id' => 283,
                'role_id' => 43,
            ),
            426 => 
            array (
                'permission_id' => 283,
                'role_id' => 47,
            ),
            427 => 
            array (
                'permission_id' => 284,
                'role_id' => 10,
            ),
            428 => 
            array (
                'permission_id' => 284,
                'role_id' => 35,
            ),
            429 => 
            array (
                'permission_id' => 284,
                'role_id' => 42,
            ),
            430 => 
            array (
                'permission_id' => 284,
                'role_id' => 43,
            ),
            431 => 
            array (
                'permission_id' => 284,
                'role_id' => 47,
            ),
            432 => 
            array (
                'permission_id' => 285,
                'role_id' => 10,
            ),
            433 => 
            array (
                'permission_id' => 285,
                'role_id' => 35,
            ),
            434 => 
            array (
                'permission_id' => 285,
                'role_id' => 42,
            ),
            435 => 
            array (
                'permission_id' => 285,
                'role_id' => 43,
            ),
            436 => 
            array (
                'permission_id' => 285,
                'role_id' => 47,
            ),
            437 => 
            array (
                'permission_id' => 286,
                'role_id' => 10,
            ),
            438 => 
            array (
                'permission_id' => 286,
                'role_id' => 35,
            ),
            439 => 
            array (
                'permission_id' => 286,
                'role_id' => 43,
            ),
            440 => 
            array (
                'permission_id' => 286,
                'role_id' => 47,
            ),
            441 => 
            array (
                'permission_id' => 291,
                'role_id' => 10,
            ),
            442 => 
            array (
                'permission_id' => 291,
                'role_id' => 38,
            ),
            443 => 
            array (
                'permission_id' => 291,
                'role_id' => 39,
            ),
            444 => 
            array (
                'permission_id' => 291,
                'role_id' => 43,
            ),
            445 => 
            array (
                'permission_id' => 291,
                'role_id' => 47,
            ),
            446 => 
            array (
                'permission_id' => 292,
                'role_id' => 10,
            ),
            447 => 
            array (
                'permission_id' => 292,
                'role_id' => 43,
            ),
            448 => 
            array (
                'permission_id' => 292,
                'role_id' => 47,
            ),
            449 => 
            array (
                'permission_id' => 293,
                'role_id' => 10,
            ),
            450 => 
            array (
                'permission_id' => 293,
                'role_id' => 43,
            ),
            451 => 
            array (
                'permission_id' => 293,
                'role_id' => 47,
            ),
            452 => 
            array (
                'permission_id' => 294,
                'role_id' => 10,
            ),
            453 => 
            array (
                'permission_id' => 294,
                'role_id' => 43,
            ),
            454 => 
            array (
                'permission_id' => 294,
                'role_id' => 47,
            ),
            455 => 
            array (
                'permission_id' => 295,
                'role_id' => 10,
            ),
            456 => 
            array (
                'permission_id' => 295,
                'role_id' => 37,
            ),
            457 => 
            array (
                'permission_id' => 295,
                'role_id' => 38,
            ),
            458 => 
            array (
                'permission_id' => 295,
                'role_id' => 39,
            ),
            459 => 
            array (
                'permission_id' => 295,
                'role_id' => 47,
            ),
            460 => 
            array (
                'permission_id' => 296,
                'role_id' => 10,
            ),
            461 => 
            array (
                'permission_id' => 296,
                'role_id' => 37,
            ),
            462 => 
            array (
                'permission_id' => 296,
                'role_id' => 38,
            ),
            463 => 
            array (
                'permission_id' => 296,
                'role_id' => 47,
            ),
            464 => 
            array (
                'permission_id' => 297,
                'role_id' => 10,
            ),
            465 => 
            array (
                'permission_id' => 297,
                'role_id' => 37,
            ),
            466 => 
            array (
                'permission_id' => 297,
                'role_id' => 38,
            ),
            467 => 
            array (
                'permission_id' => 297,
                'role_id' => 47,
            ),
            468 => 
            array (
                'permission_id' => 298,
                'role_id' => 10,
            ),
            469 => 
            array (
                'permission_id' => 298,
                'role_id' => 37,
            ),
            470 => 
            array (
                'permission_id' => 298,
                'role_id' => 38,
            ),
            471 => 
            array (
                'permission_id' => 298,
                'role_id' => 47,
            ),
            472 => 
            array (
                'permission_id' => 299,
                'role_id' => 10,
            ),
            473 => 
            array (
                'permission_id' => 299,
                'role_id' => 37,
            ),
            474 => 
            array (
                'permission_id' => 299,
                'role_id' => 38,
            ),
            475 => 
            array (
                'permission_id' => 299,
                'role_id' => 47,
            ),
            476 => 
            array (
                'permission_id' => 300,
                'role_id' => 10,
            ),
            477 => 
            array (
                'permission_id' => 300,
                'role_id' => 37,
            ),
            478 => 
            array (
                'permission_id' => 300,
                'role_id' => 38,
            ),
            479 => 
            array (
                'permission_id' => 300,
                'role_id' => 47,
            ),
            480 => 
            array (
                'permission_id' => 301,
                'role_id' => 10,
            ),
            481 => 
            array (
                'permission_id' => 301,
                'role_id' => 37,
            ),
            482 => 
            array (
                'permission_id' => 301,
                'role_id' => 38,
            ),
            483 => 
            array (
                'permission_id' => 301,
                'role_id' => 47,
            ),
            484 => 
            array (
                'permission_id' => 302,
                'role_id' => 10,
            ),
            485 => 
            array (
                'permission_id' => 302,
                'role_id' => 37,
            ),
            486 => 
            array (
                'permission_id' => 302,
                'role_id' => 38,
            ),
            487 => 
            array (
                'permission_id' => 302,
                'role_id' => 47,
            ),
            488 => 
            array (
                'permission_id' => 303,
                'role_id' => 10,
            ),
            489 => 
            array (
                'permission_id' => 303,
                'role_id' => 37,
            ),
            490 => 
            array (
                'permission_id' => 303,
                'role_id' => 38,
            ),
            491 => 
            array (
                'permission_id' => 303,
                'role_id' => 47,
            ),
            492 => 
            array (
                'permission_id' => 304,
                'role_id' => 10,
            ),
            493 => 
            array (
                'permission_id' => 304,
                'role_id' => 37,
            ),
            494 => 
            array (
                'permission_id' => 304,
                'role_id' => 38,
            ),
            495 => 
            array (
                'permission_id' => 304,
                'role_id' => 47,
            ),
            496 => 
            array (
                'permission_id' => 306,
                'role_id' => 10,
            ),
            497 => 
            array (
                'permission_id' => 306,
                'role_id' => 35,
            ),
            498 => 
            array (
                'permission_id' => 306,
                'role_id' => 38,
            ),
            499 => 
            array (
                'permission_id' => 306,
                'role_id' => 42,
            ),
        ));
        \DB::table('role_has_permissions')->insert(array (
            0 => 
            array (
                'permission_id' => 306,
                'role_id' => 43,
            ),
            1 => 
            array (
                'permission_id' => 306,
                'role_id' => 45,
            ),
            2 => 
            array (
                'permission_id' => 306,
                'role_id' => 47,
            ),
            3 => 
            array (
                'permission_id' => 307,
                'role_id' => 10,
            ),
            4 => 
            array (
                'permission_id' => 307,
                'role_id' => 35,
            ),
            5 => 
            array (
                'permission_id' => 307,
                'role_id' => 42,
            ),
            6 => 
            array (
                'permission_id' => 307,
                'role_id' => 43,
            ),
            7 => 
            array (
                'permission_id' => 307,
                'role_id' => 45,
            ),
            8 => 
            array (
                'permission_id' => 307,
                'role_id' => 47,
            ),
            9 => 
            array (
                'permission_id' => 308,
                'role_id' => 10,
            ),
            10 => 
            array (
                'permission_id' => 308,
                'role_id' => 35,
            ),
            11 => 
            array (
                'permission_id' => 308,
                'role_id' => 42,
            ),
            12 => 
            array (
                'permission_id' => 308,
                'role_id' => 43,
            ),
            13 => 
            array (
                'permission_id' => 308,
                'role_id' => 45,
            ),
            14 => 
            array (
                'permission_id' => 308,
                'role_id' => 47,
            ),
            15 => 
            array (
                'permission_id' => 309,
                'role_id' => 10,
            ),
            16 => 
            array (
                'permission_id' => 309,
                'role_id' => 35,
            ),
            17 => 
            array (
                'permission_id' => 309,
                'role_id' => 43,
            ),
            18 => 
            array (
                'permission_id' => 309,
                'role_id' => 45,
            ),
            19 => 
            array (
                'permission_id' => 309,
                'role_id' => 47,
            ),
            20 => 
            array (
                'permission_id' => 310,
                'role_id' => 10,
            ),
            21 => 
            array (
                'permission_id' => 310,
                'role_id' => 38,
            ),
            22 => 
            array (
                'permission_id' => 310,
                'role_id' => 39,
            ),
            23 => 
            array (
                'permission_id' => 310,
                'role_id' => 40,
            ),
            24 => 
            array (
                'permission_id' => 310,
                'role_id' => 41,
            ),
            25 => 
            array (
                'permission_id' => 310,
                'role_id' => 42,
            ),
            26 => 
            array (
                'permission_id' => 310,
                'role_id' => 43,
            ),
            27 => 
            array (
                'permission_id' => 310,
                'role_id' => 44,
            ),
            28 => 
            array (
                'permission_id' => 310,
                'role_id' => 47,
            ),
            29 => 
            array (
                'permission_id' => 311,
                'role_id' => 10,
            ),
            30 => 
            array (
                'permission_id' => 311,
                'role_id' => 42,
            ),
            31 => 
            array (
                'permission_id' => 311,
                'role_id' => 43,
            ),
            32 => 
            array (
                'permission_id' => 311,
                'role_id' => 47,
            ),
            33 => 
            array (
                'permission_id' => 312,
                'role_id' => 10,
            ),
            34 => 
            array (
                'permission_id' => 312,
                'role_id' => 42,
            ),
            35 => 
            array (
                'permission_id' => 312,
                'role_id' => 43,
            ),
            36 => 
            array (
                'permission_id' => 312,
                'role_id' => 44,
            ),
            37 => 
            array (
                'permission_id' => 312,
                'role_id' => 47,
            ),
            38 => 
            array (
                'permission_id' => 313,
                'role_id' => 10,
            ),
            39 => 
            array (
                'permission_id' => 313,
                'role_id' => 42,
            ),
            40 => 
            array (
                'permission_id' => 313,
                'role_id' => 43,
            ),
            41 => 
            array (
                'permission_id' => 313,
                'role_id' => 47,
            ),
            42 => 
            array (
                'permission_id' => 314,
                'role_id' => 10,
            ),
            43 => 
            array (
                'permission_id' => 314,
                'role_id' => 43,
            ),
            44 => 
            array (
                'permission_id' => 314,
                'role_id' => 47,
            ),
            45 => 
            array (
                'permission_id' => 315,
                'role_id' => 10,
            ),
            46 => 
            array (
                'permission_id' => 315,
                'role_id' => 43,
            ),
            47 => 
            array (
                'permission_id' => 315,
                'role_id' => 47,
            ),
            48 => 
            array (
                'permission_id' => 316,
                'role_id' => 10,
            ),
            49 => 
            array (
                'permission_id' => 316,
                'role_id' => 37,
            ),
            50 => 
            array (
                'permission_id' => 316,
                'role_id' => 38,
            ),
            51 => 
            array (
                'permission_id' => 316,
                'role_id' => 39,
            ),
            52 => 
            array (
                'permission_id' => 316,
                'role_id' => 40,
            ),
            53 => 
            array (
                'permission_id' => 316,
                'role_id' => 42,
            ),
            54 => 
            array (
                'permission_id' => 316,
                'role_id' => 43,
            ),
            55 => 
            array (
                'permission_id' => 316,
                'role_id' => 47,
            ),
            56 => 
            array (
                'permission_id' => 317,
                'role_id' => 10,
            ),
            57 => 
            array (
                'permission_id' => 317,
                'role_id' => 37,
            ),
            58 => 
            array (
                'permission_id' => 317,
                'role_id' => 38,
            ),
            59 => 
            array (
                'permission_id' => 317,
                'role_id' => 42,
            ),
            60 => 
            array (
                'permission_id' => 317,
                'role_id' => 43,
            ),
            61 => 
            array (
                'permission_id' => 317,
                'role_id' => 47,
            ),
            62 => 
            array (
                'permission_id' => 318,
                'role_id' => 10,
            ),
            63 => 
            array (
                'permission_id' => 318,
                'role_id' => 37,
            ),
            64 => 
            array (
                'permission_id' => 318,
                'role_id' => 38,
            ),
            65 => 
            array (
                'permission_id' => 318,
                'role_id' => 42,
            ),
            66 => 
            array (
                'permission_id' => 318,
                'role_id' => 43,
            ),
            67 => 
            array (
                'permission_id' => 318,
                'role_id' => 47,
            ),
            68 => 
            array (
                'permission_id' => 319,
                'role_id' => 10,
            ),
            69 => 
            array (
                'permission_id' => 319,
                'role_id' => 38,
            ),
            70 => 
            array (
                'permission_id' => 319,
                'role_id' => 39,
            ),
            71 => 
            array (
                'permission_id' => 319,
                'role_id' => 43,
            ),
            72 => 
            array (
                'permission_id' => 319,
                'role_id' => 47,
            ),
            73 => 
            array (
                'permission_id' => 320,
                'role_id' => 10,
            ),
            74 => 
            array (
                'permission_id' => 320,
                'role_id' => 43,
            ),
            75 => 
            array (
                'permission_id' => 320,
                'role_id' => 47,
            ),
            76 => 
            array (
                'permission_id' => 321,
                'role_id' => 10,
            ),
            77 => 
            array (
                'permission_id' => 321,
                'role_id' => 43,
            ),
            78 => 
            array (
                'permission_id' => 321,
                'role_id' => 47,
            ),
            79 => 
            array (
                'permission_id' => 322,
                'role_id' => 10,
            ),
            80 => 
            array (
                'permission_id' => 322,
                'role_id' => 43,
            ),
            81 => 
            array (
                'permission_id' => 322,
                'role_id' => 47,
            ),
            82 => 
            array (
                'permission_id' => 323,
                'role_id' => 10,
            ),
            83 => 
            array (
                'permission_id' => 323,
                'role_id' => 35,
            ),
            84 => 
            array (
                'permission_id' => 323,
                'role_id' => 38,
            ),
            85 => 
            array (
                'permission_id' => 323,
                'role_id' => 39,
            ),
            86 => 
            array (
                'permission_id' => 323,
                'role_id' => 40,
            ),
            87 => 
            array (
                'permission_id' => 323,
                'role_id' => 41,
            ),
            88 => 
            array (
                'permission_id' => 323,
                'role_id' => 42,
            ),
            89 => 
            array (
                'permission_id' => 323,
                'role_id' => 43,
            ),
            90 => 
            array (
                'permission_id' => 323,
                'role_id' => 47,
            ),
            91 => 
            array (
                'permission_id' => 324,
                'role_id' => 10,
            ),
            92 => 
            array (
                'permission_id' => 324,
                'role_id' => 35,
            ),
            93 => 
            array (
                'permission_id' => 324,
                'role_id' => 42,
            ),
            94 => 
            array (
                'permission_id' => 324,
                'role_id' => 43,
            ),
            95 => 
            array (
                'permission_id' => 324,
                'role_id' => 47,
            ),
            96 => 
            array (
                'permission_id' => 325,
                'role_id' => 10,
            ),
            97 => 
            array (
                'permission_id' => 325,
                'role_id' => 35,
            ),
            98 => 
            array (
                'permission_id' => 325,
                'role_id' => 42,
            ),
            99 => 
            array (
                'permission_id' => 325,
                'role_id' => 43,
            ),
            100 => 
            array (
                'permission_id' => 325,
                'role_id' => 47,
            ),
            101 => 
            array (
                'permission_id' => 326,
                'role_id' => 10,
            ),
            102 => 
            array (
                'permission_id' => 326,
                'role_id' => 35,
            ),
            103 => 
            array (
                'permission_id' => 326,
                'role_id' => 43,
            ),
            104 => 
            array (
                'permission_id' => 326,
                'role_id' => 47,
            ),
            105 => 
            array (
                'permission_id' => 327,
                'role_id' => 10,
            ),
            106 => 
            array (
                'permission_id' => 327,
                'role_id' => 35,
            ),
            107 => 
            array (
                'permission_id' => 327,
                'role_id' => 37,
            ),
            108 => 
            array (
                'permission_id' => 327,
                'role_id' => 38,
            ),
            109 => 
            array (
                'permission_id' => 327,
                'role_id' => 39,
            ),
            110 => 
            array (
                'permission_id' => 327,
                'role_id' => 40,
            ),
            111 => 
            array (
                'permission_id' => 327,
                'role_id' => 41,
            ),
            112 => 
            array (
                'permission_id' => 327,
                'role_id' => 42,
            ),
            113 => 
            array (
                'permission_id' => 327,
                'role_id' => 47,
            ),
            114 => 
            array (
                'permission_id' => 328,
                'role_id' => 10,
            ),
            115 => 
            array (
                'permission_id' => 328,
                'role_id' => 37,
            ),
            116 => 
            array (
                'permission_id' => 328,
                'role_id' => 38,
            ),
            117 => 
            array (
                'permission_id' => 328,
                'role_id' => 42,
            ),
            118 => 
            array (
                'permission_id' => 328,
                'role_id' => 47,
            ),
            119 => 
            array (
                'permission_id' => 329,
                'role_id' => 10,
            ),
            120 => 
            array (
                'permission_id' => 329,
                'role_id' => 37,
            ),
            121 => 
            array (
                'permission_id' => 329,
                'role_id' => 38,
            ),
            122 => 
            array (
                'permission_id' => 329,
                'role_id' => 42,
            ),
            123 => 
            array (
                'permission_id' => 329,
                'role_id' => 47,
            ),
            124 => 
            array (
                'permission_id' => 330,
                'role_id' => 10,
            ),
            125 => 
            array (
                'permission_id' => 330,
                'role_id' => 37,
            ),
            126 => 
            array (
                'permission_id' => 330,
                'role_id' => 38,
            ),
            127 => 
            array (
                'permission_id' => 330,
                'role_id' => 39,
            ),
            128 => 
            array (
                'permission_id' => 330,
                'role_id' => 40,
            ),
            129 => 
            array (
                'permission_id' => 330,
                'role_id' => 41,
            ),
            130 => 
            array (
                'permission_id' => 330,
                'role_id' => 42,
            ),
            131 => 
            array (
                'permission_id' => 330,
                'role_id' => 47,
            ),
            132 => 
            array (
                'permission_id' => 331,
                'role_id' => 10,
            ),
            133 => 
            array (
                'permission_id' => 331,
                'role_id' => 37,
            ),
            134 => 
            array (
                'permission_id' => 331,
                'role_id' => 47,
            ),
            135 => 
            array (
                'permission_id' => 332,
                'role_id' => 10,
            ),
            136 => 
            array (
                'permission_id' => 332,
                'role_id' => 37,
            ),
            137 => 
            array (
                'permission_id' => 332,
                'role_id' => 47,
            ),
            138 => 
            array (
                'permission_id' => 333,
                'role_id' => 10,
            ),
            139 => 
            array (
                'permission_id' => 333,
                'role_id' => 37,
            ),
            140 => 
            array (
                'permission_id' => 333,
                'role_id' => 47,
            ),
            141 => 
            array (
                'permission_id' => 334,
                'role_id' => 10,
            ),
            142 => 
            array (
                'permission_id' => 334,
                'role_id' => 37,
            ),
            143 => 
            array (
                'permission_id' => 334,
                'role_id' => 38,
            ),
            144 => 
            array (
                'permission_id' => 334,
                'role_id' => 39,
            ),
            145 => 
            array (
                'permission_id' => 334,
                'role_id' => 41,
            ),
            146 => 
            array (
                'permission_id' => 334,
                'role_id' => 47,
            ),
            147 => 
            array (
                'permission_id' => 335,
                'role_id' => 10,
            ),
            148 => 
            array (
                'permission_id' => 335,
                'role_id' => 37,
            ),
            149 => 
            array (
                'permission_id' => 335,
                'role_id' => 38,
            ),
            150 => 
            array (
                'permission_id' => 335,
                'role_id' => 47,
            ),
            151 => 
            array (
                'permission_id' => 336,
                'role_id' => 10,
            ),
            152 => 
            array (
                'permission_id' => 336,
                'role_id' => 37,
            ),
            153 => 
            array (
                'permission_id' => 336,
                'role_id' => 38,
            ),
            154 => 
            array (
                'permission_id' => 336,
                'role_id' => 47,
            ),
            155 => 
            array (
                'permission_id' => 337,
                'role_id' => 10,
            ),
            156 => 
            array (
                'permission_id' => 337,
                'role_id' => 37,
            ),
            157 => 
            array (
                'permission_id' => 337,
                'role_id' => 38,
            ),
            158 => 
            array (
                'permission_id' => 337,
                'role_id' => 47,
            ),
            159 => 
            array (
                'permission_id' => 338,
                'role_id' => 10,
            ),
            160 => 
            array (
                'permission_id' => 338,
                'role_id' => 38,
            ),
            161 => 
            array (
                'permission_id' => 338,
                'role_id' => 43,
            ),
            162 => 
            array (
                'permission_id' => 338,
                'role_id' => 44,
            ),
            163 => 
            array (
                'permission_id' => 338,
                'role_id' => 47,
            ),
            164 => 
            array (
                'permission_id' => 338,
                'role_id' => 48,
            ),
            165 => 
            array (
                'permission_id' => 339,
                'role_id' => 10,
            ),
            166 => 
            array (
                'permission_id' => 339,
                'role_id' => 43,
            ),
            167 => 
            array (
                'permission_id' => 339,
                'role_id' => 44,
            ),
            168 => 
            array (
                'permission_id' => 339,
                'role_id' => 47,
            ),
            169 => 
            array (
                'permission_id' => 339,
                'role_id' => 48,
            ),
            170 => 
            array (
                'permission_id' => 340,
                'role_id' => 10,
            ),
            171 => 
            array (
                'permission_id' => 340,
                'role_id' => 43,
            ),
            172 => 
            array (
                'permission_id' => 340,
                'role_id' => 44,
            ),
            173 => 
            array (
                'permission_id' => 340,
                'role_id' => 47,
            ),
            174 => 
            array (
                'permission_id' => 340,
                'role_id' => 48,
            ),
            175 => 
            array (
                'permission_id' => 341,
                'role_id' => 10,
            ),
            176 => 
            array (
                'permission_id' => 341,
                'role_id' => 43,
            ),
            177 => 
            array (
                'permission_id' => 341,
                'role_id' => 47,
            ),
            178 => 
            array (
                'permission_id' => 342,
                'role_id' => 10,
            ),
            179 => 
            array (
                'permission_id' => 342,
                'role_id' => 43,
            ),
            180 => 
            array (
                'permission_id' => 342,
                'role_id' => 47,
            ),
            181 => 
            array (
                'permission_id' => 343,
                'role_id' => 10,
            ),
            182 => 
            array (
                'permission_id' => 343,
                'role_id' => 43,
            ),
            183 => 
            array (
                'permission_id' => 343,
                'role_id' => 47,
            ),
            184 => 
            array (
                'permission_id' => 344,
                'role_id' => 10,
            ),
            185 => 
            array (
                'permission_id' => 344,
                'role_id' => 43,
            ),
            186 => 
            array (
                'permission_id' => 344,
                'role_id' => 47,
            ),
            187 => 
            array (
                'permission_id' => 345,
                'role_id' => 10,
            ),
            188 => 
            array (
                'permission_id' => 345,
                'role_id' => 43,
            ),
            189 => 
            array (
                'permission_id' => 345,
                'role_id' => 47,
            ),
            190 => 
            array (
                'permission_id' => 346,
                'role_id' => 10,
            ),
            191 => 
            array (
                'permission_id' => 346,
                'role_id' => 43,
            ),
            192 => 
            array (
                'permission_id' => 346,
                'role_id' => 47,
            ),
            193 => 
            array (
                'permission_id' => 347,
                'role_id' => 10,
            ),
            194 => 
            array (
                'permission_id' => 347,
                'role_id' => 43,
            ),
            195 => 
            array (
                'permission_id' => 347,
                'role_id' => 44,
            ),
            196 => 
            array (
                'permission_id' => 347,
                'role_id' => 47,
            ),
            197 => 
            array (
                'permission_id' => 347,
                'role_id' => 48,
            ),
            198 => 
            array (
                'permission_id' => 348,
                'role_id' => 10,
            ),
            199 => 
            array (
                'permission_id' => 348,
                'role_id' => 43,
            ),
            200 => 
            array (
                'permission_id' => 348,
                'role_id' => 44,
            ),
            201 => 
            array (
                'permission_id' => 348,
                'role_id' => 47,
            ),
            202 => 
            array (
                'permission_id' => 348,
                'role_id' => 48,
            ),
            203 => 
            array (
                'permission_id' => 349,
                'role_id' => 10,
            ),
            204 => 
            array (
                'permission_id' => 349,
                'role_id' => 38,
            ),
            205 => 
            array (
                'permission_id' => 349,
                'role_id' => 43,
            ),
            206 => 
            array (
                'permission_id' => 349,
                'role_id' => 44,
            ),
            207 => 
            array (
                'permission_id' => 349,
                'role_id' => 47,
            ),
            208 => 
            array (
                'permission_id' => 349,
                'role_id' => 48,
            ),
            209 => 
            array (
                'permission_id' => 350,
                'role_id' => 10,
            ),
            210 => 
            array (
                'permission_id' => 350,
                'role_id' => 43,
            ),
            211 => 
            array (
                'permission_id' => 350,
                'role_id' => 44,
            ),
            212 => 
            array (
                'permission_id' => 350,
                'role_id' => 47,
            ),
            213 => 
            array (
                'permission_id' => 350,
                'role_id' => 48,
            ),
            214 => 
            array (
                'permission_id' => 351,
                'role_id' => 10,
            ),
            215 => 
            array (
                'permission_id' => 351,
                'role_id' => 43,
            ),
            216 => 
            array (
                'permission_id' => 351,
                'role_id' => 44,
            ),
            217 => 
            array (
                'permission_id' => 351,
                'role_id' => 47,
            ),
            218 => 
            array (
                'permission_id' => 351,
                'role_id' => 48,
            ),
            219 => 
            array (
                'permission_id' => 352,
                'role_id' => 10,
            ),
            220 => 
            array (
                'permission_id' => 352,
                'role_id' => 43,
            ),
            221 => 
            array (
                'permission_id' => 352,
                'role_id' => 47,
            ),
            222 => 
            array (
                'permission_id' => 353,
                'role_id' => 10,
            ),
            223 => 
            array (
                'permission_id' => 353,
                'role_id' => 43,
            ),
            224 => 
            array (
                'permission_id' => 353,
                'role_id' => 47,
            ),
            225 => 
            array (
                'permission_id' => 354,
                'role_id' => 10,
            ),
            226 => 
            array (
                'permission_id' => 354,
                'role_id' => 43,
            ),
            227 => 
            array (
                'permission_id' => 354,
                'role_id' => 47,
            ),
            228 => 
            array (
                'permission_id' => 355,
                'role_id' => 10,
            ),
            229 => 
            array (
                'permission_id' => 355,
                'role_id' => 43,
            ),
            230 => 
            array (
                'permission_id' => 355,
                'role_id' => 47,
            ),
            231 => 
            array (
                'permission_id' => 356,
                'role_id' => 10,
            ),
            232 => 
            array (
                'permission_id' => 356,
                'role_id' => 43,
            ),
            233 => 
            array (
                'permission_id' => 356,
                'role_id' => 47,
            ),
            234 => 
            array (
                'permission_id' => 357,
                'role_id' => 10,
            ),
            235 => 
            array (
                'permission_id' => 357,
                'role_id' => 43,
            ),
            236 => 
            array (
                'permission_id' => 357,
                'role_id' => 47,
            ),
            237 => 
            array (
                'permission_id' => 358,
                'role_id' => 10,
            ),
            238 => 
            array (
                'permission_id' => 358,
                'role_id' => 43,
            ),
            239 => 
            array (
                'permission_id' => 358,
                'role_id' => 44,
            ),
            240 => 
            array (
                'permission_id' => 358,
                'role_id' => 47,
            ),
            241 => 
            array (
                'permission_id' => 358,
                'role_id' => 48,
            ),
            242 => 
            array (
                'permission_id' => 359,
                'role_id' => 10,
            ),
            243 => 
            array (
                'permission_id' => 359,
                'role_id' => 43,
            ),
            244 => 
            array (
                'permission_id' => 359,
                'role_id' => 44,
            ),
            245 => 
            array (
                'permission_id' => 359,
                'role_id' => 47,
            ),
            246 => 
            array (
                'permission_id' => 359,
                'role_id' => 48,
            ),
            247 => 
            array (
                'permission_id' => 360,
                'role_id' => 10,
            ),
            248 => 
            array (
                'permission_id' => 360,
                'role_id' => 38,
            ),
            249 => 
            array (
                'permission_id' => 360,
                'role_id' => 40,
            ),
            250 => 
            array (
                'permission_id' => 360,
                'role_id' => 43,
            ),
            251 => 
            array (
                'permission_id' => 360,
                'role_id' => 44,
            ),
            252 => 
            array (
                'permission_id' => 360,
                'role_id' => 47,
            ),
            253 => 
            array (
                'permission_id' => 360,
                'role_id' => 48,
            ),
            254 => 
            array (
                'permission_id' => 361,
                'role_id' => 10,
            ),
            255 => 
            array (
                'permission_id' => 361,
                'role_id' => 43,
            ),
            256 => 
            array (
                'permission_id' => 361,
                'role_id' => 44,
            ),
            257 => 
            array (
                'permission_id' => 361,
                'role_id' => 47,
            ),
            258 => 
            array (
                'permission_id' => 361,
                'role_id' => 48,
            ),
            259 => 
            array (
                'permission_id' => 362,
                'role_id' => 10,
            ),
            260 => 
            array (
                'permission_id' => 362,
                'role_id' => 43,
            ),
            261 => 
            array (
                'permission_id' => 362,
                'role_id' => 44,
            ),
            262 => 
            array (
                'permission_id' => 362,
                'role_id' => 47,
            ),
            263 => 
            array (
                'permission_id' => 362,
                'role_id' => 48,
            ),
            264 => 
            array (
                'permission_id' => 363,
                'role_id' => 10,
            ),
            265 => 
            array (
                'permission_id' => 363,
                'role_id' => 43,
            ),
            266 => 
            array (
                'permission_id' => 363,
                'role_id' => 44,
            ),
            267 => 
            array (
                'permission_id' => 363,
                'role_id' => 47,
            ),
            268 => 
            array (
                'permission_id' => 363,
                'role_id' => 48,
            ),
            269 => 
            array (
                'permission_id' => 364,
                'role_id' => 10,
            ),
            270 => 
            array (
                'permission_id' => 364,
                'role_id' => 43,
            ),
            271 => 
            array (
                'permission_id' => 364,
                'role_id' => 47,
            ),
            272 => 
            array (
                'permission_id' => 365,
                'role_id' => 10,
            ),
            273 => 
            array (
                'permission_id' => 365,
                'role_id' => 43,
            ),
            274 => 
            array (
                'permission_id' => 365,
                'role_id' => 44,
            ),
            275 => 
            array (
                'permission_id' => 365,
                'role_id' => 47,
            ),
            276 => 
            array (
                'permission_id' => 365,
                'role_id' => 48,
            ),
            277 => 
            array (
                'permission_id' => 366,
                'role_id' => 10,
            ),
            278 => 
            array (
                'permission_id' => 366,
                'role_id' => 43,
            ),
            279 => 
            array (
                'permission_id' => 366,
                'role_id' => 44,
            ),
            280 => 
            array (
                'permission_id' => 366,
                'role_id' => 47,
            ),
            281 => 
            array (
                'permission_id' => 366,
                'role_id' => 48,
            ),
            282 => 
            array (
                'permission_id' => 367,
                'role_id' => 10,
            ),
            283 => 
            array (
                'permission_id' => 367,
                'role_id' => 43,
            ),
            284 => 
            array (
                'permission_id' => 367,
                'role_id' => 44,
            ),
            285 => 
            array (
                'permission_id' => 367,
                'role_id' => 47,
            ),
            286 => 
            array (
                'permission_id' => 367,
                'role_id' => 48,
            ),
            287 => 
            array (
                'permission_id' => 368,
                'role_id' => 10,
            ),
            288 => 
            array (
                'permission_id' => 368,
                'role_id' => 43,
            ),
            289 => 
            array (
                'permission_id' => 368,
                'role_id' => 44,
            ),
            290 => 
            array (
                'permission_id' => 368,
                'role_id' => 47,
            ),
            291 => 
            array (
                'permission_id' => 368,
                'role_id' => 48,
            ),
            292 => 
            array (
                'permission_id' => 369,
                'role_id' => 10,
            ),
            293 => 
            array (
                'permission_id' => 369,
                'role_id' => 43,
            ),
            294 => 
            array (
                'permission_id' => 369,
                'role_id' => 44,
            ),
            295 => 
            array (
                'permission_id' => 369,
                'role_id' => 47,
            ),
            296 => 
            array (
                'permission_id' => 369,
                'role_id' => 48,
            ),
            297 => 
            array (
                'permission_id' => 370,
                'role_id' => 10,
            ),
            298 => 
            array (
                'permission_id' => 370,
                'role_id' => 38,
            ),
            299 => 
            array (
                'permission_id' => 370,
                'role_id' => 39,
            ),
            300 => 
            array (
                'permission_id' => 370,
                'role_id' => 40,
            ),
            301 => 
            array (
                'permission_id' => 370,
                'role_id' => 41,
            ),
            302 => 
            array (
                'permission_id' => 370,
                'role_id' => 43,
            ),
            303 => 
            array (
                'permission_id' => 370,
                'role_id' => 44,
            ),
            304 => 
            array (
                'permission_id' => 370,
                'role_id' => 47,
            ),
            305 => 
            array (
                'permission_id' => 370,
                'role_id' => 48,
            ),
            306 => 
            array (
                'permission_id' => 371,
                'role_id' => 10,
            ),
            307 => 
            array (
                'permission_id' => 371,
                'role_id' => 43,
            ),
            308 => 
            array (
                'permission_id' => 371,
                'role_id' => 44,
            ),
            309 => 
            array (
                'permission_id' => 371,
                'role_id' => 47,
            ),
            310 => 
            array (
                'permission_id' => 371,
                'role_id' => 48,
            ),
            311 => 
            array (
                'permission_id' => 372,
                'role_id' => 10,
            ),
            312 => 
            array (
                'permission_id' => 372,
                'role_id' => 43,
            ),
            313 => 
            array (
                'permission_id' => 372,
                'role_id' => 44,
            ),
            314 => 
            array (
                'permission_id' => 372,
                'role_id' => 47,
            ),
            315 => 
            array (
                'permission_id' => 372,
                'role_id' => 48,
            ),
            316 => 
            array (
                'permission_id' => 373,
                'role_id' => 10,
            ),
            317 => 
            array (
                'permission_id' => 373,
                'role_id' => 43,
            ),
            318 => 
            array (
                'permission_id' => 373,
                'role_id' => 44,
            ),
            319 => 
            array (
                'permission_id' => 373,
                'role_id' => 47,
            ),
            320 => 
            array (
                'permission_id' => 373,
                'role_id' => 48,
            ),
            321 => 
            array (
                'permission_id' => 374,
                'role_id' => 10,
            ),
            322 => 
            array (
                'permission_id' => 374,
                'role_id' => 43,
            ),
            323 => 
            array (
                'permission_id' => 374,
                'role_id' => 47,
            ),
            324 => 
            array (
                'permission_id' => 375,
                'role_id' => 10,
            ),
            325 => 
            array (
                'permission_id' => 375,
                'role_id' => 43,
            ),
            326 => 
            array (
                'permission_id' => 375,
                'role_id' => 44,
            ),
            327 => 
            array (
                'permission_id' => 375,
                'role_id' => 47,
            ),
            328 => 
            array (
                'permission_id' => 375,
                'role_id' => 48,
            ),
            329 => 
            array (
                'permission_id' => 376,
                'role_id' => 10,
            ),
            330 => 
            array (
                'permission_id' => 376,
                'role_id' => 43,
            ),
            331 => 
            array (
                'permission_id' => 376,
                'role_id' => 44,
            ),
            332 => 
            array (
                'permission_id' => 376,
                'role_id' => 47,
            ),
            333 => 
            array (
                'permission_id' => 376,
                'role_id' => 48,
            ),
            334 => 
            array (
                'permission_id' => 377,
                'role_id' => 10,
            ),
            335 => 
            array (
                'permission_id' => 377,
                'role_id' => 43,
            ),
            336 => 
            array (
                'permission_id' => 377,
                'role_id' => 44,
            ),
            337 => 
            array (
                'permission_id' => 377,
                'role_id' => 47,
            ),
            338 => 
            array (
                'permission_id' => 377,
                'role_id' => 48,
            ),
            339 => 
            array (
                'permission_id' => 378,
                'role_id' => 10,
            ),
            340 => 
            array (
                'permission_id' => 378,
                'role_id' => 43,
            ),
            341 => 
            array (
                'permission_id' => 378,
                'role_id' => 44,
            ),
            342 => 
            array (
                'permission_id' => 378,
                'role_id' => 47,
            ),
            343 => 
            array (
                'permission_id' => 378,
                'role_id' => 48,
            ),
            344 => 
            array (
                'permission_id' => 379,
                'role_id' => 10,
            ),
            345 => 
            array (
                'permission_id' => 379,
                'role_id' => 43,
            ),
            346 => 
            array (
                'permission_id' => 379,
                'role_id' => 44,
            ),
            347 => 
            array (
                'permission_id' => 379,
                'role_id' => 47,
            ),
            348 => 
            array (
                'permission_id' => 379,
                'role_id' => 48,
            ),
            349 => 
            array (
                'permission_id' => 380,
                'role_id' => 10,
            ),
            350 => 
            array (
                'permission_id' => 380,
                'role_id' => 39,
            ),
            351 => 
            array (
                'permission_id' => 380,
                'role_id' => 43,
            ),
            352 => 
            array (
                'permission_id' => 380,
                'role_id' => 47,
            ),
            353 => 
            array (
                'permission_id' => 381,
                'role_id' => 10,
            ),
            354 => 
            array (
                'permission_id' => 381,
                'role_id' => 43,
            ),
            355 => 
            array (
                'permission_id' => 381,
                'role_id' => 47,
            ),
            356 => 
            array (
                'permission_id' => 382,
                'role_id' => 10,
            ),
            357 => 
            array (
                'permission_id' => 382,
                'role_id' => 43,
            ),
            358 => 
            array (
                'permission_id' => 382,
                'role_id' => 47,
            ),
            359 => 
            array (
                'permission_id' => 383,
                'role_id' => 10,
            ),
            360 => 
            array (
                'permission_id' => 383,
                'role_id' => 43,
            ),
            361 => 
            array (
                'permission_id' => 383,
                'role_id' => 47,
            ),
            362 => 
            array (
                'permission_id' => 384,
                'role_id' => 10,
            ),
            363 => 
            array (
                'permission_id' => 384,
                'role_id' => 39,
            ),
            364 => 
            array (
                'permission_id' => 384,
                'role_id' => 43,
            ),
            365 => 
            array (
                'permission_id' => 384,
                'role_id' => 47,
            ),
            366 => 
            array (
                'permission_id' => 385,
                'role_id' => 10,
            ),
            367 => 
            array (
                'permission_id' => 385,
                'role_id' => 43,
            ),
            368 => 
            array (
                'permission_id' => 385,
                'role_id' => 47,
            ),
            369 => 
            array (
                'permission_id' => 386,
                'role_id' => 10,
            ),
            370 => 
            array (
                'permission_id' => 386,
                'role_id' => 43,
            ),
            371 => 
            array (
                'permission_id' => 386,
                'role_id' => 47,
            ),
            372 => 
            array (
                'permission_id' => 387,
                'role_id' => 10,
            ),
            373 => 
            array (
                'permission_id' => 387,
                'role_id' => 43,
            ),
            374 => 
            array (
                'permission_id' => 387,
                'role_id' => 47,
            ),
            375 => 
            array (
                'permission_id' => 388,
                'role_id' => 10,
            ),
            376 => 
            array (
                'permission_id' => 388,
                'role_id' => 38,
            ),
            377 => 
            array (
                'permission_id' => 388,
                'role_id' => 43,
            ),
            378 => 
            array (
                'permission_id' => 388,
                'role_id' => 44,
            ),
            379 => 
            array (
                'permission_id' => 388,
                'role_id' => 47,
            ),
            380 => 
            array (
                'permission_id' => 388,
                'role_id' => 48,
            ),
            381 => 
            array (
                'permission_id' => 389,
                'role_id' => 10,
            ),
            382 => 
            array (
                'permission_id' => 389,
                'role_id' => 43,
            ),
            383 => 
            array (
                'permission_id' => 389,
                'role_id' => 44,
            ),
            384 => 
            array (
                'permission_id' => 389,
                'role_id' => 47,
            ),
            385 => 
            array (
                'permission_id' => 389,
                'role_id' => 48,
            ),
            386 => 
            array (
                'permission_id' => 390,
                'role_id' => 10,
            ),
            387 => 
            array (
                'permission_id' => 390,
                'role_id' => 43,
            ),
            388 => 
            array (
                'permission_id' => 390,
                'role_id' => 44,
            ),
            389 => 
            array (
                'permission_id' => 390,
                'role_id' => 47,
            ),
            390 => 
            array (
                'permission_id' => 390,
                'role_id' => 48,
            ),
            391 => 
            array (
                'permission_id' => 391,
                'role_id' => 10,
            ),
            392 => 
            array (
                'permission_id' => 391,
                'role_id' => 43,
            ),
            393 => 
            array (
                'permission_id' => 391,
                'role_id' => 44,
            ),
            394 => 
            array (
                'permission_id' => 391,
                'role_id' => 47,
            ),
            395 => 
            array (
                'permission_id' => 391,
                'role_id' => 48,
            ),
            396 => 
            array (
                'permission_id' => 392,
                'role_id' => 10,
            ),
            397 => 
            array (
                'permission_id' => 392,
                'role_id' => 37,
            ),
            398 => 
            array (
                'permission_id' => 392,
                'role_id' => 38,
            ),
            399 => 
            array (
                'permission_id' => 392,
                'role_id' => 45,
            ),
            400 => 
            array (
                'permission_id' => 392,
                'role_id' => 46,
            ),
            401 => 
            array (
                'permission_id' => 392,
                'role_id' => 47,
            ),
            402 => 
            array (
                'permission_id' => 393,
                'role_id' => 10,
            ),
            403 => 
            array (
                'permission_id' => 393,
                'role_id' => 35,
            ),
            404 => 
            array (
                'permission_id' => 393,
                'role_id' => 37,
            ),
            405 => 
            array (
                'permission_id' => 393,
                'role_id' => 38,
            ),
            406 => 
            array (
                'permission_id' => 393,
                'role_id' => 39,
            ),
            407 => 
            array (
                'permission_id' => 393,
                'role_id' => 42,
            ),
            408 => 
            array (
                'permission_id' => 393,
                'role_id' => 43,
            ),
            409 => 
            array (
                'permission_id' => 393,
                'role_id' => 44,
            ),
            410 => 
            array (
                'permission_id' => 393,
                'role_id' => 47,
            ),
            411 => 
            array (
                'permission_id' => 393,
                'role_id' => 48,
            ),
            412 => 
            array (
                'permission_id' => 394,
                'role_id' => 10,
            ),
            413 => 
            array (
                'permission_id' => 394,
                'role_id' => 37,
            ),
            414 => 
            array (
                'permission_id' => 394,
                'role_id' => 38,
            ),
            415 => 
            array (
                'permission_id' => 394,
                'role_id' => 39,
            ),
            416 => 
            array (
                'permission_id' => 394,
                'role_id' => 43,
            ),
            417 => 
            array (
                'permission_id' => 394,
                'role_id' => 44,
            ),
            418 => 
            array (
                'permission_id' => 394,
                'role_id' => 47,
            ),
            419 => 
            array (
                'permission_id' => 394,
                'role_id' => 48,
            ),
            420 => 
            array (
                'permission_id' => 395,
                'role_id' => 10,
            ),
            421 => 
            array (
                'permission_id' => 395,
                'role_id' => 35,
            ),
            422 => 
            array (
                'permission_id' => 395,
                'role_id' => 37,
            ),
            423 => 
            array (
                'permission_id' => 395,
                'role_id' => 38,
            ),
            424 => 
            array (
                'permission_id' => 395,
                'role_id' => 39,
            ),
            425 => 
            array (
                'permission_id' => 395,
                'role_id' => 42,
            ),
            426 => 
            array (
                'permission_id' => 395,
                'role_id' => 43,
            ),
            427 => 
            array (
                'permission_id' => 395,
                'role_id' => 44,
            ),
            428 => 
            array (
                'permission_id' => 395,
                'role_id' => 47,
            ),
            429 => 
            array (
                'permission_id' => 395,
                'role_id' => 48,
            ),
            430 => 
            array (
                'permission_id' => 396,
                'role_id' => 10,
            ),
            431 => 
            array (
                'permission_id' => 396,
                'role_id' => 37,
            ),
            432 => 
            array (
                'permission_id' => 396,
                'role_id' => 38,
            ),
            433 => 
            array (
                'permission_id' => 396,
                'role_id' => 39,
            ),
            434 => 
            array (
                'permission_id' => 396,
                'role_id' => 43,
            ),
            435 => 
            array (
                'permission_id' => 396,
                'role_id' => 44,
            ),
            436 => 
            array (
                'permission_id' => 396,
                'role_id' => 47,
            ),
            437 => 
            array (
                'permission_id' => 396,
                'role_id' => 48,
            ),
            438 => 
            array (
                'permission_id' => 397,
                'role_id' => 10,
            ),
            439 => 
            array (
                'permission_id' => 397,
                'role_id' => 38,
            ),
            440 => 
            array (
                'permission_id' => 397,
                'role_id' => 39,
            ),
            441 => 
            array (
                'permission_id' => 397,
                'role_id' => 40,
            ),
            442 => 
            array (
                'permission_id' => 397,
                'role_id' => 41,
            ),
            443 => 
            array (
                'permission_id' => 397,
                'role_id' => 47,
            ),
            444 => 
            array (
                'permission_id' => 398,
                'role_id' => 10,
            ),
            445 => 
            array (
                'permission_id' => 398,
                'role_id' => 40,
            ),
            446 => 
            array (
                'permission_id' => 398,
                'role_id' => 41,
            ),
            447 => 
            array (
                'permission_id' => 398,
                'role_id' => 47,
            ),
            448 => 
            array (
                'permission_id' => 399,
                'role_id' => 10,
            ),
            449 => 
            array (
                'permission_id' => 399,
                'role_id' => 40,
            ),
            450 => 
            array (
                'permission_id' => 399,
                'role_id' => 41,
            ),
            451 => 
            array (
                'permission_id' => 399,
                'role_id' => 47,
            ),
            452 => 
            array (
                'permission_id' => 400,
                'role_id' => 10,
            ),
            453 => 
            array (
                'permission_id' => 400,
                'role_id' => 40,
            ),
            454 => 
            array (
                'permission_id' => 400,
                'role_id' => 47,
            ),
            455 => 
            array (
                'permission_id' => 401,
                'role_id' => 10,
            ),
            456 => 
            array (
                'permission_id' => 401,
                'role_id' => 39,
            ),
            457 => 
            array (
                'permission_id' => 401,
                'role_id' => 40,
            ),
            458 => 
            array (
                'permission_id' => 401,
                'role_id' => 47,
            ),
            459 => 
            array (
                'permission_id' => 402,
                'role_id' => 10,
            ),
            460 => 
            array (
                'permission_id' => 402,
                'role_id' => 39,
            ),
            461 => 
            array (
                'permission_id' => 402,
                'role_id' => 40,
            ),
            462 => 
            array (
                'permission_id' => 402,
                'role_id' => 47,
            ),
            463 => 
            array (
                'permission_id' => 403,
                'role_id' => 10,
            ),
            464 => 
            array (
                'permission_id' => 403,
                'role_id' => 39,
            ),
            465 => 
            array (
                'permission_id' => 403,
                'role_id' => 40,
            ),
            466 => 
            array (
                'permission_id' => 403,
                'role_id' => 47,
            ),
            467 => 
            array (
                'permission_id' => 404,
                'role_id' => 10,
            ),
            468 => 
            array (
                'permission_id' => 404,
                'role_id' => 39,
            ),
            469 => 
            array (
                'permission_id' => 404,
                'role_id' => 40,
            ),
            470 => 
            array (
                'permission_id' => 404,
                'role_id' => 47,
            ),
            471 => 
            array (
                'permission_id' => 405,
                'role_id' => 10,
            ),
            472 => 
            array (
                'permission_id' => 405,
                'role_id' => 39,
            ),
            473 => 
            array (
                'permission_id' => 405,
                'role_id' => 40,
            ),
            474 => 
            array (
                'permission_id' => 405,
                'role_id' => 47,
            ),
            475 => 
            array (
                'permission_id' => 406,
                'role_id' => 10,
            ),
            476 => 
            array (
                'permission_id' => 406,
                'role_id' => 39,
            ),
            477 => 
            array (
                'permission_id' => 406,
                'role_id' => 40,
            ),
            478 => 
            array (
                'permission_id' => 406,
                'role_id' => 47,
            ),
            479 => 
            array (
                'permission_id' => 407,
                'role_id' => 10,
            ),
            480 => 
            array (
                'permission_id' => 407,
                'role_id' => 38,
            ),
            481 => 
            array (
                'permission_id' => 407,
                'role_id' => 39,
            ),
            482 => 
            array (
                'permission_id' => 407,
                'role_id' => 40,
            ),
            483 => 
            array (
                'permission_id' => 407,
                'role_id' => 41,
            ),
            484 => 
            array (
                'permission_id' => 407,
                'role_id' => 43,
            ),
            485 => 
            array (
                'permission_id' => 407,
                'role_id' => 44,
            ),
            486 => 
            array (
                'permission_id' => 407,
                'role_id' => 47,
            ),
            487 => 
            array (
                'permission_id' => 407,
                'role_id' => 48,
            ),
            488 => 
            array (
                'permission_id' => 408,
                'role_id' => 10,
            ),
            489 => 
            array (
                'permission_id' => 408,
                'role_id' => 43,
            ),
            490 => 
            array (
                'permission_id' => 408,
                'role_id' => 44,
            ),
            491 => 
            array (
                'permission_id' => 408,
                'role_id' => 47,
            ),
            492 => 
            array (
                'permission_id' => 408,
                'role_id' => 48,
            ),
            493 => 
            array (
                'permission_id' => 409,
                'role_id' => 10,
            ),
            494 => 
            array (
                'permission_id' => 409,
                'role_id' => 43,
            ),
            495 => 
            array (
                'permission_id' => 409,
                'role_id' => 44,
            ),
            496 => 
            array (
                'permission_id' => 409,
                'role_id' => 47,
            ),
            497 => 
            array (
                'permission_id' => 409,
                'role_id' => 48,
            ),
            498 => 
            array (
                'permission_id' => 410,
                'role_id' => 10,
            ),
            499 => 
            array (
                'permission_id' => 410,
                'role_id' => 43,
            ),
        ));
        \DB::table('role_has_permissions')->insert(array (
            0 => 
            array (
                'permission_id' => 410,
                'role_id' => 47,
            ),
            1 => 
            array (
                'permission_id' => 410,
                'role_id' => 48,
            ),
            2 => 
            array (
                'permission_id' => 415,
                'role_id' => 10,
            ),
            3 => 
            array (
                'permission_id' => 415,
                'role_id' => 38,
            ),
            4 => 
            array (
                'permission_id' => 415,
                'role_id' => 39,
            ),
            5 => 
            array (
                'permission_id' => 415,
                'role_id' => 40,
            ),
            6 => 
            array (
                'permission_id' => 415,
                'role_id' => 41,
            ),
            7 => 
            array (
                'permission_id' => 415,
                'role_id' => 43,
            ),
            8 => 
            array (
                'permission_id' => 415,
                'role_id' => 44,
            ),
            9 => 
            array (
                'permission_id' => 415,
                'role_id' => 47,
            ),
            10 => 
            array (
                'permission_id' => 416,
                'role_id' => 10,
            ),
            11 => 
            array (
                'permission_id' => 416,
                'role_id' => 40,
            ),
            12 => 
            array (
                'permission_id' => 416,
                'role_id' => 41,
            ),
            13 => 
            array (
                'permission_id' => 416,
                'role_id' => 47,
            ),
            14 => 
            array (
                'permission_id' => 417,
                'role_id' => 10,
            ),
            15 => 
            array (
                'permission_id' => 417,
                'role_id' => 40,
            ),
            16 => 
            array (
                'permission_id' => 417,
                'role_id' => 41,
            ),
            17 => 
            array (
                'permission_id' => 417,
                'role_id' => 47,
            ),
            18 => 
            array (
                'permission_id' => 418,
                'role_id' => 10,
            ),
            19 => 
            array (
                'permission_id' => 418,
                'role_id' => 40,
            ),
            20 => 
            array (
                'permission_id' => 418,
                'role_id' => 47,
            ),
            21 => 
            array (
                'permission_id' => 419,
                'role_id' => 10,
            ),
            22 => 
            array (
                'permission_id' => 419,
                'role_id' => 35,
            ),
            23 => 
            array (
                'permission_id' => 419,
                'role_id' => 38,
            ),
            24 => 
            array (
                'permission_id' => 419,
                'role_id' => 39,
            ),
            25 => 
            array (
                'permission_id' => 419,
                'role_id' => 40,
            ),
            26 => 
            array (
                'permission_id' => 419,
                'role_id' => 41,
            ),
            27 => 
            array (
                'permission_id' => 419,
                'role_id' => 42,
            ),
            28 => 
            array (
                'permission_id' => 419,
                'role_id' => 43,
            ),
            29 => 
            array (
                'permission_id' => 419,
                'role_id' => 44,
            ),
            30 => 
            array (
                'permission_id' => 419,
                'role_id' => 47,
            ),
            31 => 
            array (
                'permission_id' => 420,
                'role_id' => 10,
            ),
            32 => 
            array (
                'permission_id' => 420,
                'role_id' => 35,
            ),
            33 => 
            array (
                'permission_id' => 420,
                'role_id' => 40,
            ),
            34 => 
            array (
                'permission_id' => 420,
                'role_id' => 41,
            ),
            35 => 
            array (
                'permission_id' => 420,
                'role_id' => 43,
            ),
            36 => 
            array (
                'permission_id' => 420,
                'role_id' => 44,
            ),
            37 => 
            array (
                'permission_id' => 420,
                'role_id' => 47,
            ),
            38 => 
            array (
                'permission_id' => 421,
                'role_id' => 10,
            ),
            39 => 
            array (
                'permission_id' => 421,
                'role_id' => 35,
            ),
            40 => 
            array (
                'permission_id' => 421,
                'role_id' => 40,
            ),
            41 => 
            array (
                'permission_id' => 421,
                'role_id' => 41,
            ),
            42 => 
            array (
                'permission_id' => 421,
                'role_id' => 43,
            ),
            43 => 
            array (
                'permission_id' => 421,
                'role_id' => 47,
            ),
            44 => 
            array (
                'permission_id' => 422,
                'role_id' => 10,
            ),
            45 => 
            array (
                'permission_id' => 422,
                'role_id' => 35,
            ),
            46 => 
            array (
                'permission_id' => 422,
                'role_id' => 40,
            ),
            47 => 
            array (
                'permission_id' => 422,
                'role_id' => 43,
            ),
            48 => 
            array (
                'permission_id' => 422,
                'role_id' => 47,
            ),
            49 => 
            array (
                'permission_id' => 427,
                'role_id' => 10,
            ),
            50 => 
            array (
                'permission_id' => 427,
                'role_id' => 38,
            ),
            51 => 
            array (
                'permission_id' => 427,
                'role_id' => 39,
            ),
            52 => 
            array (
                'permission_id' => 427,
                'role_id' => 40,
            ),
            53 => 
            array (
                'permission_id' => 427,
                'role_id' => 41,
            ),
            54 => 
            array (
                'permission_id' => 427,
                'role_id' => 47,
            ),
            55 => 
            array (
                'permission_id' => 428,
                'role_id' => 10,
            ),
            56 => 
            array (
                'permission_id' => 428,
                'role_id' => 38,
            ),
            57 => 
            array (
                'permission_id' => 428,
                'role_id' => 40,
            ),
            58 => 
            array (
                'permission_id' => 428,
                'role_id' => 41,
            ),
            59 => 
            array (
                'permission_id' => 428,
                'role_id' => 47,
            ),
            60 => 
            array (
                'permission_id' => 429,
                'role_id' => 10,
            ),
            61 => 
            array (
                'permission_id' => 429,
                'role_id' => 38,
            ),
            62 => 
            array (
                'permission_id' => 429,
                'role_id' => 40,
            ),
            63 => 
            array (
                'permission_id' => 429,
                'role_id' => 41,
            ),
            64 => 
            array (
                'permission_id' => 429,
                'role_id' => 47,
            ),
            65 => 
            array (
                'permission_id' => 430,
                'role_id' => 10,
            ),
            66 => 
            array (
                'permission_id' => 430,
                'role_id' => 40,
            ),
            67 => 
            array (
                'permission_id' => 430,
                'role_id' => 41,
            ),
            68 => 
            array (
                'permission_id' => 430,
                'role_id' => 47,
            ),
            69 => 
            array (
                'permission_id' => 431,
                'role_id' => 10,
            ),
            70 => 
            array (
                'permission_id' => 431,
                'role_id' => 38,
            ),
            71 => 
            array (
                'permission_id' => 431,
                'role_id' => 39,
            ),
            72 => 
            array (
                'permission_id' => 431,
                'role_id' => 40,
            ),
            73 => 
            array (
                'permission_id' => 431,
                'role_id' => 41,
            ),
            74 => 
            array (
                'permission_id' => 431,
                'role_id' => 47,
            ),
            75 => 
            array (
                'permission_id' => 432,
                'role_id' => 10,
            ),
            76 => 
            array (
                'permission_id' => 432,
                'role_id' => 38,
            ),
            77 => 
            array (
                'permission_id' => 432,
                'role_id' => 40,
            ),
            78 => 
            array (
                'permission_id' => 432,
                'role_id' => 41,
            ),
            79 => 
            array (
                'permission_id' => 432,
                'role_id' => 47,
            ),
            80 => 
            array (
                'permission_id' => 433,
                'role_id' => 10,
            ),
            81 => 
            array (
                'permission_id' => 433,
                'role_id' => 38,
            ),
            82 => 
            array (
                'permission_id' => 433,
                'role_id' => 40,
            ),
            83 => 
            array (
                'permission_id' => 433,
                'role_id' => 41,
            ),
            84 => 
            array (
                'permission_id' => 433,
                'role_id' => 47,
            ),
            85 => 
            array (
                'permission_id' => 434,
                'role_id' => 10,
            ),
            86 => 
            array (
                'permission_id' => 434,
                'role_id' => 40,
            ),
            87 => 
            array (
                'permission_id' => 434,
                'role_id' => 47,
            ),
            88 => 
            array (
                'permission_id' => 435,
                'role_id' => 10,
            ),
            89 => 
            array (
                'permission_id' => 435,
                'role_id' => 37,
            ),
            90 => 
            array (
                'permission_id' => 435,
                'role_id' => 38,
            ),
            91 => 
            array (
                'permission_id' => 435,
                'role_id' => 39,
            ),
            92 => 
            array (
                'permission_id' => 435,
                'role_id' => 40,
            ),
            93 => 
            array (
                'permission_id' => 435,
                'role_id' => 41,
            ),
            94 => 
            array (
                'permission_id' => 435,
                'role_id' => 42,
            ),
            95 => 
            array (
                'permission_id' => 435,
                'role_id' => 45,
            ),
            96 => 
            array (
                'permission_id' => 435,
                'role_id' => 47,
            ),
            97 => 
            array (
                'permission_id' => 436,
                'role_id' => 10,
            ),
            98 => 
            array (
                'permission_id' => 436,
                'role_id' => 37,
            ),
            99 => 
            array (
                'permission_id' => 436,
                'role_id' => 38,
            ),
            100 => 
            array (
                'permission_id' => 436,
                'role_id' => 47,
            ),
            101 => 
            array (
                'permission_id' => 437,
                'role_id' => 10,
            ),
            102 => 
            array (
                'permission_id' => 437,
                'role_id' => 37,
            ),
            103 => 
            array (
                'permission_id' => 437,
                'role_id' => 38,
            ),
            104 => 
            array (
                'permission_id' => 437,
                'role_id' => 47,
            ),
            105 => 
            array (
                'permission_id' => 438,
                'role_id' => 10,
            ),
            106 => 
            array (
                'permission_id' => 438,
                'role_id' => 37,
            ),
            107 => 
            array (
                'permission_id' => 438,
                'role_id' => 38,
            ),
            108 => 
            array (
                'permission_id' => 438,
                'role_id' => 47,
            ),
            109 => 
            array (
                'permission_id' => 439,
                'role_id' => 10,
            ),
            110 => 
            array (
                'permission_id' => 439,
                'role_id' => 37,
            ),
            111 => 
            array (
                'permission_id' => 439,
                'role_id' => 38,
            ),
            112 => 
            array (
                'permission_id' => 439,
                'role_id' => 39,
            ),
            113 => 
            array (
                'permission_id' => 439,
                'role_id' => 47,
            ),
            114 => 
            array (
                'permission_id' => 440,
                'role_id' => 10,
            ),
            115 => 
            array (
                'permission_id' => 440,
                'role_id' => 37,
            ),
            116 => 
            array (
                'permission_id' => 440,
                'role_id' => 38,
            ),
            117 => 
            array (
                'permission_id' => 440,
                'role_id' => 47,
            ),
            118 => 
            array (
                'permission_id' => 441,
                'role_id' => 10,
            ),
            119 => 
            array (
                'permission_id' => 441,
                'role_id' => 37,
            ),
            120 => 
            array (
                'permission_id' => 441,
                'role_id' => 38,
            ),
            121 => 
            array (
                'permission_id' => 441,
                'role_id' => 47,
            ),
            122 => 
            array (
                'permission_id' => 442,
                'role_id' => 10,
            ),
            123 => 
            array (
                'permission_id' => 442,
                'role_id' => 37,
            ),
            124 => 
            array (
                'permission_id' => 442,
                'role_id' => 38,
            ),
            125 => 
            array (
                'permission_id' => 442,
                'role_id' => 47,
            ),
            126 => 
            array (
                'permission_id' => 443,
                'role_id' => 10,
            ),
            127 => 
            array (
                'permission_id' => 443,
                'role_id' => 37,
            ),
            128 => 
            array (
                'permission_id' => 443,
                'role_id' => 38,
            ),
            129 => 
            array (
                'permission_id' => 443,
                'role_id' => 39,
            ),
            130 => 
            array (
                'permission_id' => 443,
                'role_id' => 47,
            ),
            131 => 
            array (
                'permission_id' => 444,
                'role_id' => 10,
            ),
            132 => 
            array (
                'permission_id' => 444,
                'role_id' => 37,
            ),
            133 => 
            array (
                'permission_id' => 444,
                'role_id' => 38,
            ),
            134 => 
            array (
                'permission_id' => 444,
                'role_id' => 47,
            ),
            135 => 
            array (
                'permission_id' => 445,
                'role_id' => 10,
            ),
            136 => 
            array (
                'permission_id' => 445,
                'role_id' => 37,
            ),
            137 => 
            array (
                'permission_id' => 445,
                'role_id' => 38,
            ),
            138 => 
            array (
                'permission_id' => 445,
                'role_id' => 47,
            ),
            139 => 
            array (
                'permission_id' => 446,
                'role_id' => 10,
            ),
            140 => 
            array (
                'permission_id' => 446,
                'role_id' => 37,
            ),
            141 => 
            array (
                'permission_id' => 446,
                'role_id' => 38,
            ),
            142 => 
            array (
                'permission_id' => 446,
                'role_id' => 47,
            ),
            143 => 
            array (
                'permission_id' => 491,
                'role_id' => 10,
            ),
            144 => 
            array (
                'permission_id' => 491,
                'role_id' => 37,
            ),
            145 => 
            array (
                'permission_id' => 491,
                'role_id' => 38,
            ),
            146 => 
            array (
                'permission_id' => 491,
                'role_id' => 39,
            ),
            147 => 
            array (
                'permission_id' => 491,
                'role_id' => 47,
            ),
            148 => 
            array (
                'permission_id' => 492,
                'role_id' => 10,
            ),
            149 => 
            array (
                'permission_id' => 492,
                'role_id' => 37,
            ),
            150 => 
            array (
                'permission_id' => 492,
                'role_id' => 38,
            ),
            151 => 
            array (
                'permission_id' => 492,
                'role_id' => 47,
            ),
            152 => 
            array (
                'permission_id' => 493,
                'role_id' => 10,
            ),
            153 => 
            array (
                'permission_id' => 493,
                'role_id' => 37,
            ),
            154 => 
            array (
                'permission_id' => 493,
                'role_id' => 38,
            ),
            155 => 
            array (
                'permission_id' => 493,
                'role_id' => 47,
            ),
            156 => 
            array (
                'permission_id' => 494,
                'role_id' => 10,
            ),
            157 => 
            array (
                'permission_id' => 494,
                'role_id' => 37,
            ),
            158 => 
            array (
                'permission_id' => 494,
                'role_id' => 38,
            ),
            159 => 
            array (
                'permission_id' => 494,
                'role_id' => 47,
            ),
            160 => 
            array (
                'permission_id' => 495,
                'role_id' => 10,
            ),
            161 => 
            array (
                'permission_id' => 495,
                'role_id' => 38,
            ),
            162 => 
            array (
                'permission_id' => 495,
                'role_id' => 39,
            ),
            163 => 
            array (
                'permission_id' => 495,
                'role_id' => 40,
            ),
            164 => 
            array (
                'permission_id' => 495,
                'role_id' => 41,
            ),
            165 => 
            array (
                'permission_id' => 495,
                'role_id' => 47,
            ),
            166 => 
            array (
                'permission_id' => 496,
                'role_id' => 10,
            ),
            167 => 
            array (
                'permission_id' => 496,
                'role_id' => 40,
            ),
            168 => 
            array (
                'permission_id' => 496,
                'role_id' => 41,
            ),
            169 => 
            array (
                'permission_id' => 496,
                'role_id' => 47,
            ),
            170 => 
            array (
                'permission_id' => 497,
                'role_id' => 10,
            ),
            171 => 
            array (
                'permission_id' => 497,
                'role_id' => 40,
            ),
            172 => 
            array (
                'permission_id' => 497,
                'role_id' => 41,
            ),
            173 => 
            array (
                'permission_id' => 497,
                'role_id' => 47,
            ),
            174 => 
            array (
                'permission_id' => 498,
                'role_id' => 10,
            ),
            175 => 
            array (
                'permission_id' => 498,
                'role_id' => 40,
            ),
            176 => 
            array (
                'permission_id' => 498,
                'role_id' => 47,
            ),
            177 => 
            array (
                'permission_id' => 499,
                'role_id' => 10,
            ),
            178 => 
            array (
                'permission_id' => 499,
                'role_id' => 39,
            ),
            179 => 
            array (
                'permission_id' => 499,
                'role_id' => 40,
            ),
            180 => 
            array (
                'permission_id' => 499,
                'role_id' => 47,
            ),
            181 => 
            array (
                'permission_id' => 500,
                'role_id' => 10,
            ),
            182 => 
            array (
                'permission_id' => 500,
                'role_id' => 39,
            ),
            183 => 
            array (
                'permission_id' => 500,
                'role_id' => 40,
            ),
            184 => 
            array (
                'permission_id' => 500,
                'role_id' => 47,
            ),
            185 => 
            array (
                'permission_id' => 501,
                'role_id' => 10,
            ),
            186 => 
            array (
                'permission_id' => 501,
                'role_id' => 39,
            ),
            187 => 
            array (
                'permission_id' => 501,
                'role_id' => 40,
            ),
            188 => 
            array (
                'permission_id' => 501,
                'role_id' => 47,
            ),
            189 => 
            array (
                'permission_id' => 502,
                'role_id' => 10,
            ),
            190 => 
            array (
                'permission_id' => 502,
                'role_id' => 38,
            ),
            191 => 
            array (
                'permission_id' => 502,
                'role_id' => 39,
            ),
            192 => 
            array (
                'permission_id' => 502,
                'role_id' => 40,
            ),
            193 => 
            array (
                'permission_id' => 502,
                'role_id' => 41,
            ),
            194 => 
            array (
                'permission_id' => 502,
                'role_id' => 47,
            ),
            195 => 
            array (
                'permission_id' => 503,
                'role_id' => 10,
            ),
            196 => 
            array (
                'permission_id' => 503,
                'role_id' => 40,
            ),
            197 => 
            array (
                'permission_id' => 503,
                'role_id' => 41,
            ),
            198 => 
            array (
                'permission_id' => 503,
                'role_id' => 47,
            ),
            199 => 
            array (
                'permission_id' => 504,
                'role_id' => 10,
            ),
            200 => 
            array (
                'permission_id' => 504,
                'role_id' => 40,
            ),
            201 => 
            array (
                'permission_id' => 504,
                'role_id' => 41,
            ),
            202 => 
            array (
                'permission_id' => 504,
                'role_id' => 47,
            ),
            203 => 
            array (
                'permission_id' => 505,
                'role_id' => 10,
            ),
            204 => 
            array (
                'permission_id' => 505,
                'role_id' => 40,
            ),
            205 => 
            array (
                'permission_id' => 505,
                'role_id' => 41,
            ),
            206 => 
            array (
                'permission_id' => 505,
                'role_id' => 47,
            ),
            207 => 
            array (
                'permission_id' => 506,
                'role_id' => 10,
            ),
            208 => 
            array (
                'permission_id' => 506,
                'role_id' => 39,
            ),
            209 => 
            array (
                'permission_id' => 506,
                'role_id' => 40,
            ),
            210 => 
            array (
                'permission_id' => 506,
                'role_id' => 47,
            ),
            211 => 
            array (
                'permission_id' => 507,
                'role_id' => 10,
            ),
            212 => 
            array (
                'permission_id' => 507,
                'role_id' => 39,
            ),
            213 => 
            array (
                'permission_id' => 507,
                'role_id' => 40,
            ),
            214 => 
            array (
                'permission_id' => 507,
                'role_id' => 47,
            ),
            215 => 
            array (
                'permission_id' => 508,
                'role_id' => 10,
            ),
            216 => 
            array (
                'permission_id' => 508,
                'role_id' => 39,
            ),
            217 => 
            array (
                'permission_id' => 508,
                'role_id' => 40,
            ),
            218 => 
            array (
                'permission_id' => 508,
                'role_id' => 47,
            ),
            219 => 
            array (
                'permission_id' => 509,
                'role_id' => 10,
            ),
            220 => 
            array (
                'permission_id' => 509,
                'role_id' => 38,
            ),
            221 => 
            array (
                'permission_id' => 509,
                'role_id' => 39,
            ),
            222 => 
            array (
                'permission_id' => 509,
                'role_id' => 40,
            ),
            223 => 
            array (
                'permission_id' => 509,
                'role_id' => 41,
            ),
            224 => 
            array (
                'permission_id' => 509,
                'role_id' => 42,
            ),
            225 => 
            array (
                'permission_id' => 509,
                'role_id' => 47,
            ),
            226 => 
            array (
                'permission_id' => 510,
                'role_id' => 10,
            ),
            227 => 
            array (
                'permission_id' => 510,
                'role_id' => 40,
            ),
            228 => 
            array (
                'permission_id' => 510,
                'role_id' => 41,
            ),
            229 => 
            array (
                'permission_id' => 510,
                'role_id' => 47,
            ),
            230 => 
            array (
                'permission_id' => 511,
                'role_id' => 10,
            ),
            231 => 
            array (
                'permission_id' => 511,
                'role_id' => 40,
            ),
            232 => 
            array (
                'permission_id' => 511,
                'role_id' => 41,
            ),
            233 => 
            array (
                'permission_id' => 511,
                'role_id' => 47,
            ),
            234 => 
            array (
                'permission_id' => 512,
                'role_id' => 10,
            ),
            235 => 
            array (
                'permission_id' => 512,
                'role_id' => 40,
            ),
            236 => 
            array (
                'permission_id' => 512,
                'role_id' => 47,
            ),
            237 => 
            array (
                'permission_id' => 513,
                'role_id' => 10,
            ),
            238 => 
            array (
                'permission_id' => 513,
                'role_id' => 39,
            ),
            239 => 
            array (
                'permission_id' => 513,
                'role_id' => 40,
            ),
            240 => 
            array (
                'permission_id' => 513,
                'role_id' => 47,
            ),
            241 => 
            array (
                'permission_id' => 514,
                'role_id' => 10,
            ),
            242 => 
            array (
                'permission_id' => 514,
                'role_id' => 39,
            ),
            243 => 
            array (
                'permission_id' => 514,
                'role_id' => 40,
            ),
            244 => 
            array (
                'permission_id' => 514,
                'role_id' => 47,
            ),
            245 => 
            array (
                'permission_id' => 515,
                'role_id' => 10,
            ),
            246 => 
            array (
                'permission_id' => 515,
                'role_id' => 35,
            ),
            247 => 
            array (
                'permission_id' => 515,
                'role_id' => 39,
            ),
            248 => 
            array (
                'permission_id' => 515,
                'role_id' => 40,
            ),
            249 => 
            array (
                'permission_id' => 515,
                'role_id' => 47,
            ),
            250 => 
            array (
                'permission_id' => 516,
                'role_id' => 10,
            ),
            251 => 
            array (
                'permission_id' => 516,
                'role_id' => 35,
            ),
            252 => 
            array (
                'permission_id' => 516,
                'role_id' => 39,
            ),
            253 => 
            array (
                'permission_id' => 516,
                'role_id' => 40,
            ),
            254 => 
            array (
                'permission_id' => 516,
                'role_id' => 47,
            ),
            255 => 
            array (
                'permission_id' => 527,
                'role_id' => 35,
            ),
            256 => 
            array (
                'permission_id' => 527,
                'role_id' => 37,
            ),
            257 => 
            array (
                'permission_id' => 527,
                'role_id' => 38,
            ),
            258 => 
            array (
                'permission_id' => 527,
                'role_id' => 39,
            ),
            259 => 
            array (
                'permission_id' => 527,
                'role_id' => 40,
            ),
            260 => 
            array (
                'permission_id' => 527,
                'role_id' => 42,
            ),
            261 => 
            array (
                'permission_id' => 527,
                'role_id' => 47,
            ),
            262 => 
            array (
                'permission_id' => 528,
                'role_id' => 37,
            ),
            263 => 
            array (
                'permission_id' => 528,
                'role_id' => 38,
            ),
            264 => 
            array (
                'permission_id' => 528,
                'role_id' => 42,
            ),
            265 => 
            array (
                'permission_id' => 528,
                'role_id' => 47,
            ),
            266 => 
            array (
                'permission_id' => 529,
                'role_id' => 39,
            ),
            267 => 
            array (
                'permission_id' => 529,
                'role_id' => 40,
            ),
            268 => 
            array (
                'permission_id' => 529,
                'role_id' => 47,
            ),
            269 => 
            array (
                'permission_id' => 530,
                'role_id' => 39,
            ),
            270 => 
            array (
                'permission_id' => 530,
                'role_id' => 40,
            ),
            271 => 
            array (
                'permission_id' => 530,
                'role_id' => 47,
            ),
            272 => 
            array (
                'permission_id' => 531,
                'role_id' => 39,
            ),
            273 => 
            array (
                'permission_id' => 531,
                'role_id' => 40,
            ),
            274 => 
            array (
                'permission_id' => 531,
                'role_id' => 47,
            ),
            275 => 
            array (
                'permission_id' => 532,
                'role_id' => 39,
            ),
            276 => 
            array (
                'permission_id' => 532,
                'role_id' => 40,
            ),
            277 => 
            array (
                'permission_id' => 532,
                'role_id' => 47,
            ),
            278 => 
            array (
                'permission_id' => 533,
                'role_id' => 39,
            ),
            279 => 
            array (
                'permission_id' => 533,
                'role_id' => 40,
            ),
            280 => 
            array (
                'permission_id' => 533,
                'role_id' => 47,
            ),
            281 => 
            array (
                'permission_id' => 534,
                'role_id' => 45,
            ),
            282 => 
            array (
                'permission_id' => 534,
                'role_id' => 47,
            ),
            283 => 
            array (
                'permission_id' => 535,
                'role_id' => 10,
            ),
            284 => 
            array (
                'permission_id' => 535,
                'role_id' => 38,
            ),
            285 => 
            array (
                'permission_id' => 535,
                'role_id' => 39,
            ),
            286 => 
            array (
                'permission_id' => 535,
                'role_id' => 40,
            ),
            287 => 
            array (
                'permission_id' => 535,
                'role_id' => 41,
            ),
            288 => 
            array (
                'permission_id' => 535,
                'role_id' => 47,
            ),
            289 => 
            array (
                'permission_id' => 536,
                'role_id' => 10,
            ),
            290 => 
            array (
                'permission_id' => 536,
                'role_id' => 40,
            ),
            291 => 
            array (
                'permission_id' => 536,
                'role_id' => 41,
            ),
            292 => 
            array (
                'permission_id' => 536,
                'role_id' => 47,
            ),
            293 => 
            array (
                'permission_id' => 537,
                'role_id' => 10,
            ),
            294 => 
            array (
                'permission_id' => 537,
                'role_id' => 40,
            ),
            295 => 
            array (
                'permission_id' => 537,
                'role_id' => 41,
            ),
            296 => 
            array (
                'permission_id' => 537,
                'role_id' => 47,
            ),
            297 => 
            array (
                'permission_id' => 538,
                'role_id' => 10,
            ),
            298 => 
            array (
                'permission_id' => 538,
                'role_id' => 40,
            ),
            299 => 
            array (
                'permission_id' => 538,
                'role_id' => 47,
            ),
            300 => 
            array (
                'permission_id' => 539,
                'role_id' => 10,
            ),
            301 => 
            array (
                'permission_id' => 539,
                'role_id' => 39,
            ),
            302 => 
            array (
                'permission_id' => 539,
                'role_id' => 40,
            ),
            303 => 
            array (
                'permission_id' => 539,
                'role_id' => 47,
            ),
            304 => 
            array (
                'permission_id' => 540,
                'role_id' => 10,
            ),
            305 => 
            array (
                'permission_id' => 540,
                'role_id' => 39,
            ),
            306 => 
            array (
                'permission_id' => 540,
                'role_id' => 40,
            ),
            307 => 
            array (
                'permission_id' => 540,
                'role_id' => 47,
            ),
            308 => 
            array (
                'permission_id' => 541,
                'role_id' => 10,
            ),
            309 => 
            array (
                'permission_id' => 541,
                'role_id' => 38,
            ),
            310 => 
            array (
                'permission_id' => 541,
                'role_id' => 39,
            ),
            311 => 
            array (
                'permission_id' => 541,
                'role_id' => 40,
            ),
            312 => 
            array (
                'permission_id' => 541,
                'role_id' => 41,
            ),
            313 => 
            array (
                'permission_id' => 541,
                'role_id' => 47,
            ),
            314 => 
            array (
                'permission_id' => 542,
                'role_id' => 10,
            ),
            315 => 
            array (
                'permission_id' => 542,
                'role_id' => 40,
            ),
            316 => 
            array (
                'permission_id' => 542,
                'role_id' => 41,
            ),
            317 => 
            array (
                'permission_id' => 542,
                'role_id' => 47,
            ),
            318 => 
            array (
                'permission_id' => 543,
                'role_id' => 10,
            ),
            319 => 
            array (
                'permission_id' => 543,
                'role_id' => 40,
            ),
            320 => 
            array (
                'permission_id' => 543,
                'role_id' => 41,
            ),
            321 => 
            array (
                'permission_id' => 543,
                'role_id' => 47,
            ),
            322 => 
            array (
                'permission_id' => 544,
                'role_id' => 10,
            ),
            323 => 
            array (
                'permission_id' => 544,
                'role_id' => 40,
            ),
            324 => 
            array (
                'permission_id' => 544,
                'role_id' => 47,
            ),
            325 => 
            array (
                'permission_id' => 545,
                'role_id' => 10,
            ),
            326 => 
            array (
                'permission_id' => 545,
                'role_id' => 39,
            ),
            327 => 
            array (
                'permission_id' => 545,
                'role_id' => 40,
            ),
            328 => 
            array (
                'permission_id' => 545,
                'role_id' => 47,
            ),
            329 => 
            array (
                'permission_id' => 546,
                'role_id' => 10,
            ),
            330 => 
            array (
                'permission_id' => 546,
                'role_id' => 39,
            ),
            331 => 
            array (
                'permission_id' => 546,
                'role_id' => 40,
            ),
            332 => 
            array (
                'permission_id' => 546,
                'role_id' => 47,
            ),
            333 => 
            array (
                'permission_id' => 547,
                'role_id' => 10,
            ),
            334 => 
            array (
                'permission_id' => 547,
                'role_id' => 38,
            ),
            335 => 
            array (
                'permission_id' => 547,
                'role_id' => 39,
            ),
            336 => 
            array (
                'permission_id' => 547,
                'role_id' => 40,
            ),
            337 => 
            array (
                'permission_id' => 547,
                'role_id' => 41,
            ),
            338 => 
            array (
                'permission_id' => 547,
                'role_id' => 47,
            ),
            339 => 
            array (
                'permission_id' => 548,
                'role_id' => 10,
            ),
            340 => 
            array (
                'permission_id' => 548,
                'role_id' => 40,
            ),
            341 => 
            array (
                'permission_id' => 548,
                'role_id' => 41,
            ),
            342 => 
            array (
                'permission_id' => 548,
                'role_id' => 47,
            ),
            343 => 
            array (
                'permission_id' => 549,
                'role_id' => 10,
            ),
            344 => 
            array (
                'permission_id' => 549,
                'role_id' => 40,
            ),
            345 => 
            array (
                'permission_id' => 549,
                'role_id' => 41,
            ),
            346 => 
            array (
                'permission_id' => 549,
                'role_id' => 47,
            ),
            347 => 
            array (
                'permission_id' => 550,
                'role_id' => 10,
            ),
            348 => 
            array (
                'permission_id' => 550,
                'role_id' => 40,
            ),
            349 => 
            array (
                'permission_id' => 550,
                'role_id' => 41,
            ),
            350 => 
            array (
                'permission_id' => 550,
                'role_id' => 47,
            ),
            351 => 
            array (
                'permission_id' => 551,
                'role_id' => 10,
            ),
            352 => 
            array (
                'permission_id' => 551,
                'role_id' => 39,
            ),
            353 => 
            array (
                'permission_id' => 551,
                'role_id' => 40,
            ),
            354 => 
            array (
                'permission_id' => 551,
                'role_id' => 47,
            ),
            355 => 
            array (
                'permission_id' => 552,
                'role_id' => 10,
            ),
            356 => 
            array (
                'permission_id' => 552,
                'role_id' => 39,
            ),
            357 => 
            array (
                'permission_id' => 552,
                'role_id' => 40,
            ),
            358 => 
            array (
                'permission_id' => 552,
                'role_id' => 47,
            ),
            359 => 
            array (
                'permission_id' => 553,
                'role_id' => 10,
            ),
            360 => 
            array (
                'permission_id' => 553,
                'role_id' => 39,
            ),
            361 => 
            array (
                'permission_id' => 553,
                'role_id' => 40,
            ),
            362 => 
            array (
                'permission_id' => 553,
                'role_id' => 41,
            ),
            363 => 
            array (
                'permission_id' => 553,
                'role_id' => 47,
            ),
            364 => 
            array (
                'permission_id' => 554,
                'role_id' => 10,
            ),
            365 => 
            array (
                'permission_id' => 554,
                'role_id' => 39,
            ),
            366 => 
            array (
                'permission_id' => 554,
                'role_id' => 40,
            ),
            367 => 
            array (
                'permission_id' => 554,
                'role_id' => 47,
            ),
            368 => 
            array (
                'permission_id' => 560,
                'role_id' => 10,
            ),
            369 => 
            array (
                'permission_id' => 560,
                'role_id' => 45,
            ),
            370 => 
            array (
                'permission_id' => 560,
                'role_id' => 47,
            ),
            371 => 
            array (
                'permission_id' => 561,
                'role_id' => 10,
            ),
            372 => 
            array (
                'permission_id' => 561,
                'role_id' => 47,
            ),
            373 => 
            array (
                'permission_id' => 562,
                'role_id' => 10,
            ),
            374 => 
            array (
                'permission_id' => 562,
                'role_id' => 47,
            ),
            375 => 
            array (
                'permission_id' => 563,
                'role_id' => 10,
            ),
            376 => 
            array (
                'permission_id' => 563,
                'role_id' => 47,
            ),
            377 => 
            array (
                'permission_id' => 564,
                'role_id' => 10,
            ),
            378 => 
            array (
                'permission_id' => 564,
                'role_id' => 47,
            ),
            379 => 
            array (
                'permission_id' => 565,
                'role_id' => 10,
            ),
            380 => 
            array (
                'permission_id' => 565,
                'role_id' => 47,
            ),
            381 => 
            array (
                'permission_id' => 566,
                'role_id' => 10,
            ),
            382 => 
            array (
                'permission_id' => 566,
                'role_id' => 37,
            ),
            383 => 
            array (
                'permission_id' => 566,
                'role_id' => 38,
            ),
            384 => 
            array (
                'permission_id' => 566,
                'role_id' => 39,
            ),
            385 => 
            array (
                'permission_id' => 566,
                'role_id' => 47,
            ),
            386 => 
            array (
                'permission_id' => 567,
                'role_id' => 10,
            ),
            387 => 
            array (
                'permission_id' => 567,
                'role_id' => 37,
            ),
            388 => 
            array (
                'permission_id' => 567,
                'role_id' => 38,
            ),
            389 => 
            array (
                'permission_id' => 567,
                'role_id' => 47,
            ),
            390 => 
            array (
                'permission_id' => 568,
                'role_id' => 10,
            ),
            391 => 
            array (
                'permission_id' => 568,
                'role_id' => 37,
            ),
            392 => 
            array (
                'permission_id' => 568,
                'role_id' => 38,
            ),
            393 => 
            array (
                'permission_id' => 568,
                'role_id' => 47,
            ),
            394 => 
            array (
                'permission_id' => 569,
                'role_id' => 10,
            ),
            395 => 
            array (
                'permission_id' => 569,
                'role_id' => 37,
            ),
            396 => 
            array (
                'permission_id' => 569,
                'role_id' => 38,
            ),
            397 => 
            array (
                'permission_id' => 569,
                'role_id' => 47,
            ),
            398 => 
            array (
                'permission_id' => 570,
                'role_id' => 10,
            ),
            399 => 
            array (
                'permission_id' => 570,
                'role_id' => 37,
            ),
            400 => 
            array (
                'permission_id' => 570,
                'role_id' => 38,
            ),
            401 => 
            array (
                'permission_id' => 570,
                'role_id' => 47,
            ),
            402 => 
            array (
                'permission_id' => 571,
                'role_id' => 10,
            ),
            403 => 
            array (
                'permission_id' => 571,
                'role_id' => 37,
            ),
            404 => 
            array (
                'permission_id' => 571,
                'role_id' => 38,
            ),
            405 => 
            array (
                'permission_id' => 571,
                'role_id' => 47,
            ),
            406 => 
            array (
                'permission_id' => 572,
                'role_id' => 10,
            ),
            407 => 
            array (
                'permission_id' => 572,
                'role_id' => 37,
            ),
            408 => 
            array (
                'permission_id' => 572,
                'role_id' => 38,
            ),
            409 => 
            array (
                'permission_id' => 572,
                'role_id' => 47,
            ),
            410 => 
            array (
                'permission_id' => 573,
                'role_id' => 10,
            ),
            411 => 
            array (
                'permission_id' => 573,
                'role_id' => 37,
            ),
            412 => 
            array (
                'permission_id' => 573,
                'role_id' => 38,
            ),
            413 => 
            array (
                'permission_id' => 573,
                'role_id' => 47,
            ),
            414 => 
            array (
                'permission_id' => 574,
                'role_id' => 10,
            ),
            415 => 
            array (
                'permission_id' => 574,
                'role_id' => 37,
            ),
            416 => 
            array (
                'permission_id' => 574,
                'role_id' => 38,
            ),
            417 => 
            array (
                'permission_id' => 574,
                'role_id' => 39,
            ),
            418 => 
            array (
                'permission_id' => 574,
                'role_id' => 41,
            ),
            419 => 
            array (
                'permission_id' => 574,
                'role_id' => 43,
            ),
            420 => 
            array (
                'permission_id' => 574,
                'role_id' => 47,
            ),
            421 => 
            array (
                'permission_id' => 575,
                'role_id' => 10,
            ),
            422 => 
            array (
                'permission_id' => 575,
                'role_id' => 37,
            ),
            423 => 
            array (
                'permission_id' => 575,
                'role_id' => 38,
            ),
            424 => 
            array (
                'permission_id' => 575,
                'role_id' => 43,
            ),
            425 => 
            array (
                'permission_id' => 575,
                'role_id' => 47,
            ),
            426 => 
            array (
                'permission_id' => 576,
                'role_id' => 10,
            ),
            427 => 
            array (
                'permission_id' => 576,
                'role_id' => 45,
            ),
            428 => 
            array (
                'permission_id' => 576,
                'role_id' => 47,
            ),
            429 => 
            array (
                'permission_id' => 577,
                'role_id' => 10,
            ),
            430 => 
            array (
                'permission_id' => 577,
                'role_id' => 45,
            ),
            431 => 
            array (
                'permission_id' => 577,
                'role_id' => 47,
            ),
            432 => 
            array (
                'permission_id' => 578,
                'role_id' => 10,
            ),
            433 => 
            array (
                'permission_id' => 578,
                'role_id' => 45,
            ),
            434 => 
            array (
                'permission_id' => 578,
                'role_id' => 47,
            ),
            435 => 
            array (
                'permission_id' => 579,
                'role_id' => 10,
            ),
            436 => 
            array (
                'permission_id' => 579,
                'role_id' => 45,
            ),
            437 => 
            array (
                'permission_id' => 579,
                'role_id' => 47,
            ),
            438 => 
            array (
                'permission_id' => 580,
                'role_id' => 10,
            ),
            439 => 
            array (
                'permission_id' => 580,
                'role_id' => 45,
            ),
            440 => 
            array (
                'permission_id' => 580,
                'role_id' => 46,
            ),
            441 => 
            array (
                'permission_id' => 580,
                'role_id' => 47,
            ),
            442 => 
            array (
                'permission_id' => 581,
                'role_id' => 10,
            ),
            443 => 
            array (
                'permission_id' => 581,
                'role_id' => 45,
            ),
            444 => 
            array (
                'permission_id' => 581,
                'role_id' => 46,
            ),
            445 => 
            array (
                'permission_id' => 581,
                'role_id' => 47,
            ),
            446 => 
            array (
                'permission_id' => 582,
                'role_id' => 10,
            ),
            447 => 
            array (
                'permission_id' => 582,
                'role_id' => 45,
            ),
            448 => 
            array (
                'permission_id' => 582,
                'role_id' => 47,
            ),
            449 => 
            array (
                'permission_id' => 583,
                'role_id' => 10,
            ),
            450 => 
            array (
                'permission_id' => 583,
                'role_id' => 45,
            ),
            451 => 
            array (
                'permission_id' => 583,
                'role_id' => 47,
            ),
            452 => 
            array (
                'permission_id' => 584,
                'role_id' => 10,
            ),
            453 => 
            array (
                'permission_id' => 584,
                'role_id' => 46,
            ),
            454 => 
            array (
                'permission_id' => 584,
                'role_id' => 47,
            ),
            455 => 
            array (
                'permission_id' => 585,
                'role_id' => 10,
            ),
            456 => 
            array (
                'permission_id' => 585,
                'role_id' => 46,
            ),
            457 => 
            array (
                'permission_id' => 585,
                'role_id' => 47,
            ),
            458 => 
            array (
                'permission_id' => 586,
                'role_id' => 10,
            ),
            459 => 
            array (
                'permission_id' => 586,
                'role_id' => 46,
            ),
            460 => 
            array (
                'permission_id' => 586,
                'role_id' => 47,
            ),
            461 => 
            array (
                'permission_id' => 587,
                'role_id' => 10,
            ),
            462 => 
            array (
                'permission_id' => 587,
                'role_id' => 47,
            ),
            463 => 
            array (
                'permission_id' => 588,
                'role_id' => 10,
            ),
            464 => 
            array (
                'permission_id' => 588,
                'role_id' => 47,
            ),
            465 => 
            array (
                'permission_id' => 589,
                'role_id' => 10,
            ),
            466 => 
            array (
                'permission_id' => 589,
                'role_id' => 47,
            ),
            467 => 
            array (
                'permission_id' => 590,
                'role_id' => 10,
            ),
            468 => 
            array (
                'permission_id' => 590,
                'role_id' => 38,
            ),
            469 => 
            array (
                'permission_id' => 590,
                'role_id' => 40,
            ),
            470 => 
            array (
                'permission_id' => 590,
                'role_id' => 43,
            ),
            471 => 
            array (
                'permission_id' => 590,
                'role_id' => 44,
            ),
            472 => 
            array (
                'permission_id' => 590,
                'role_id' => 47,
            ),
            473 => 
            array (
                'permission_id' => 590,
                'role_id' => 48,
            ),
            474 => 
            array (
                'permission_id' => 591,
                'role_id' => 10,
            ),
            475 => 
            array (
                'permission_id' => 591,
                'role_id' => 43,
            ),
            476 => 
            array (
                'permission_id' => 591,
                'role_id' => 44,
            ),
            477 => 
            array (
                'permission_id' => 591,
                'role_id' => 47,
            ),
            478 => 
            array (
                'permission_id' => 591,
                'role_id' => 48,
            ),
            479 => 
            array (
                'permission_id' => 592,
                'role_id' => 10,
            ),
            480 => 
            array (
                'permission_id' => 592,
                'role_id' => 43,
            ),
            481 => 
            array (
                'permission_id' => 592,
                'role_id' => 44,
            ),
            482 => 
            array (
                'permission_id' => 592,
                'role_id' => 47,
            ),
            483 => 
            array (
                'permission_id' => 592,
                'role_id' => 48,
            ),
            484 => 
            array (
                'permission_id' => 593,
                'role_id' => 10,
            ),
            485 => 
            array (
                'permission_id' => 593,
                'role_id' => 43,
            ),
            486 => 
            array (
                'permission_id' => 593,
                'role_id' => 44,
            ),
            487 => 
            array (
                'permission_id' => 593,
                'role_id' => 47,
            ),
            488 => 
            array (
                'permission_id' => 593,
                'role_id' => 48,
            ),
            489 => 
            array (
                'permission_id' => 594,
                'role_id' => 10,
            ),
            490 => 
            array (
                'permission_id' => 594,
                'role_id' => 43,
            ),
            491 => 
            array (
                'permission_id' => 594,
                'role_id' => 47,
            ),
            492 => 
            array (
                'permission_id' => 595,
                'role_id' => 10,
            ),
            493 => 
            array (
                'permission_id' => 595,
                'role_id' => 44,
            ),
            494 => 
            array (
                'permission_id' => 595,
                'role_id' => 47,
            ),
            495 => 
            array (
                'permission_id' => 595,
                'role_id' => 48,
            ),
            496 => 
            array (
                'permission_id' => 596,
                'role_id' => 10,
            ),
            497 => 
            array (
                'permission_id' => 596,
                'role_id' => 44,
            ),
            498 => 
            array (
                'permission_id' => 596,
                'role_id' => 47,
            ),
            499 => 
            array (
                'permission_id' => 597,
                'role_id' => 10,
            ),
        ));
        \DB::table('role_has_permissions')->insert(array (
            0 => 
            array (
                'permission_id' => 597,
                'role_id' => 44,
            ),
            1 => 
            array (
                'permission_id' => 597,
                'role_id' => 47,
            ),
            2 => 
            array (
                'permission_id' => 598,
                'role_id' => 10,
            ),
            3 => 
            array (
                'permission_id' => 598,
                'role_id' => 38,
            ),
            4 => 
            array (
                'permission_id' => 598,
                'role_id' => 39,
            ),
            5 => 
            array (
                'permission_id' => 598,
                'role_id' => 40,
            ),
            6 => 
            array (
                'permission_id' => 598,
                'role_id' => 41,
            ),
            7 => 
            array (
                'permission_id' => 598,
                'role_id' => 42,
            ),
            8 => 
            array (
                'permission_id' => 598,
                'role_id' => 43,
            ),
            9 => 
            array (
                'permission_id' => 598,
                'role_id' => 44,
            ),
            10 => 
            array (
                'permission_id' => 598,
                'role_id' => 47,
            ),
            11 => 
            array (
                'permission_id' => 598,
                'role_id' => 48,
            ),
            12 => 
            array (
                'permission_id' => 599,
                'role_id' => 10,
            ),
            13 => 
            array (
                'permission_id' => 599,
                'role_id' => 42,
            ),
            14 => 
            array (
                'permission_id' => 599,
                'role_id' => 43,
            ),
            15 => 
            array (
                'permission_id' => 599,
                'role_id' => 44,
            ),
            16 => 
            array (
                'permission_id' => 599,
                'role_id' => 47,
            ),
            17 => 
            array (
                'permission_id' => 599,
                'role_id' => 48,
            ),
            18 => 
            array (
                'permission_id' => 600,
                'role_id' => 10,
            ),
            19 => 
            array (
                'permission_id' => 600,
                'role_id' => 38,
            ),
            20 => 
            array (
                'permission_id' => 600,
                'role_id' => 42,
            ),
            21 => 
            array (
                'permission_id' => 600,
                'role_id' => 43,
            ),
            22 => 
            array (
                'permission_id' => 600,
                'role_id' => 44,
            ),
            23 => 
            array (
                'permission_id' => 600,
                'role_id' => 47,
            ),
            24 => 
            array (
                'permission_id' => 600,
                'role_id' => 48,
            ),
            25 => 
            array (
                'permission_id' => 601,
                'role_id' => 10,
            ),
            26 => 
            array (
                'permission_id' => 601,
                'role_id' => 42,
            ),
            27 => 
            array (
                'permission_id' => 601,
                'role_id' => 43,
            ),
            28 => 
            array (
                'permission_id' => 601,
                'role_id' => 44,
            ),
            29 => 
            array (
                'permission_id' => 601,
                'role_id' => 47,
            ),
            30 => 
            array (
                'permission_id' => 601,
                'role_id' => 48,
            ),
            31 => 
            array (
                'permission_id' => 602,
                'role_id' => 10,
            ),
            32 => 
            array (
                'permission_id' => 602,
                'role_id' => 43,
            ),
            33 => 
            array (
                'permission_id' => 602,
                'role_id' => 47,
            ),
            34 => 
            array (
                'permission_id' => 603,
                'role_id' => 10,
            ),
            35 => 
            array (
                'permission_id' => 603,
                'role_id' => 43,
            ),
            36 => 
            array (
                'permission_id' => 603,
                'role_id' => 47,
            ),
            37 => 
            array (
                'permission_id' => 604,
                'role_id' => 10,
            ),
            38 => 
            array (
                'permission_id' => 604,
                'role_id' => 43,
            ),
            39 => 
            array (
                'permission_id' => 604,
                'role_id' => 47,
            ),
            40 => 
            array (
                'permission_id' => 605,
                'role_id' => 10,
            ),
            41 => 
            array (
                'permission_id' => 605,
                'role_id' => 47,
            ),
            42 => 
            array (
                'permission_id' => 606,
                'role_id' => 10,
            ),
            43 => 
            array (
                'permission_id' => 606,
                'role_id' => 43,
            ),
            44 => 
            array (
                'permission_id' => 606,
                'role_id' => 47,
            ),
            45 => 
            array (
                'permission_id' => 607,
                'role_id' => 10,
            ),
            46 => 
            array (
                'permission_id' => 607,
                'role_id' => 45,
            ),
            47 => 
            array (
                'permission_id' => 607,
                'role_id' => 46,
            ),
            48 => 
            array (
                'permission_id' => 607,
                'role_id' => 47,
            ),
            49 => 
            array (
                'permission_id' => 608,
                'role_id' => 10,
            ),
            50 => 
            array (
                'permission_id' => 608,
                'role_id' => 45,
            ),
            51 => 
            array (
                'permission_id' => 608,
                'role_id' => 46,
            ),
            52 => 
            array (
                'permission_id' => 608,
                'role_id' => 47,
            ),
            53 => 
            array (
                'permission_id' => 609,
                'role_id' => 10,
            ),
            54 => 
            array (
                'permission_id' => 609,
                'role_id' => 45,
            ),
            55 => 
            array (
                'permission_id' => 609,
                'role_id' => 46,
            ),
            56 => 
            array (
                'permission_id' => 609,
                'role_id' => 47,
            ),
            57 => 
            array (
                'permission_id' => 610,
                'role_id' => 10,
            ),
            58 => 
            array (
                'permission_id' => 610,
                'role_id' => 45,
            ),
            59 => 
            array (
                'permission_id' => 610,
                'role_id' => 47,
            ),
            60 => 
            array (
                'permission_id' => 611,
                'role_id' => 10,
            ),
            61 => 
            array (
                'permission_id' => 611,
                'role_id' => 45,
            ),
            62 => 
            array (
                'permission_id' => 611,
                'role_id' => 46,
            ),
            63 => 
            array (
                'permission_id' => 611,
                'role_id' => 47,
            ),
            64 => 
            array (
                'permission_id' => 612,
                'role_id' => 10,
            ),
            65 => 
            array (
                'permission_id' => 612,
                'role_id' => 45,
            ),
            66 => 
            array (
                'permission_id' => 612,
                'role_id' => 46,
            ),
            67 => 
            array (
                'permission_id' => 612,
                'role_id' => 47,
            ),
            68 => 
            array (
                'permission_id' => 613,
                'role_id' => 10,
            ),
            69 => 
            array (
                'permission_id' => 613,
                'role_id' => 45,
            ),
            70 => 
            array (
                'permission_id' => 613,
                'role_id' => 47,
            ),
            71 => 
            array (
                'permission_id' => 614,
                'role_id' => 10,
            ),
            72 => 
            array (
                'permission_id' => 614,
                'role_id' => 45,
            ),
            73 => 
            array (
                'permission_id' => 614,
                'role_id' => 47,
            ),
            74 => 
            array (
                'permission_id' => 615,
                'role_id' => 10,
            ),
            75 => 
            array (
                'permission_id' => 615,
                'role_id' => 45,
            ),
            76 => 
            array (
                'permission_id' => 615,
                'role_id' => 46,
            ),
            77 => 
            array (
                'permission_id' => 615,
                'role_id' => 47,
            ),
            78 => 
            array (
                'permission_id' => 616,
                'role_id' => 10,
            ),
            79 => 
            array (
                'permission_id' => 616,
                'role_id' => 45,
            ),
            80 => 
            array (
                'permission_id' => 616,
                'role_id' => 46,
            ),
            81 => 
            array (
                'permission_id' => 616,
                'role_id' => 47,
            ),
            82 => 
            array (
                'permission_id' => 617,
                'role_id' => 10,
            ),
            83 => 
            array (
                'permission_id' => 617,
                'role_id' => 45,
            ),
            84 => 
            array (
                'permission_id' => 617,
                'role_id' => 46,
            ),
            85 => 
            array (
                'permission_id' => 617,
                'role_id' => 47,
            ),
            86 => 
            array (
                'permission_id' => 618,
                'role_id' => 10,
            ),
            87 => 
            array (
                'permission_id' => 618,
                'role_id' => 45,
            ),
            88 => 
            array (
                'permission_id' => 618,
                'role_id' => 47,
            ),
            89 => 
            array (
                'permission_id' => 619,
                'role_id' => 10,
            ),
            90 => 
            array (
                'permission_id' => 619,
                'role_id' => 45,
            ),
            91 => 
            array (
                'permission_id' => 619,
                'role_id' => 46,
            ),
            92 => 
            array (
                'permission_id' => 619,
                'role_id' => 47,
            ),
            93 => 
            array (
                'permission_id' => 620,
                'role_id' => 10,
            ),
            94 => 
            array (
                'permission_id' => 620,
                'role_id' => 45,
            ),
            95 => 
            array (
                'permission_id' => 620,
                'role_id' => 46,
            ),
            96 => 
            array (
                'permission_id' => 620,
                'role_id' => 47,
            ),
            97 => 
            array (
                'permission_id' => 621,
                'role_id' => 10,
            ),
            98 => 
            array (
                'permission_id' => 621,
                'role_id' => 45,
            ),
            99 => 
            array (
                'permission_id' => 621,
                'role_id' => 46,
            ),
            100 => 
            array (
                'permission_id' => 621,
                'role_id' => 47,
            ),
            101 => 
            array (
                'permission_id' => 622,
                'role_id' => 10,
            ),
            102 => 
            array (
                'permission_id' => 622,
                'role_id' => 45,
            ),
            103 => 
            array (
                'permission_id' => 622,
                'role_id' => 47,
            ),
            104 => 
            array (
                'permission_id' => 623,
                'role_id' => 10,
            ),
            105 => 
            array (
                'permission_id' => 623,
                'role_id' => 45,
            ),
            106 => 
            array (
                'permission_id' => 623,
                'role_id' => 46,
            ),
            107 => 
            array (
                'permission_id' => 623,
                'role_id' => 47,
            ),
            108 => 
            array (
                'permission_id' => 624,
                'role_id' => 10,
            ),
            109 => 
            array (
                'permission_id' => 624,
                'role_id' => 45,
            ),
            110 => 
            array (
                'permission_id' => 624,
                'role_id' => 46,
            ),
            111 => 
            array (
                'permission_id' => 624,
                'role_id' => 47,
            ),
            112 => 
            array (
                'permission_id' => 625,
                'role_id' => 10,
            ),
            113 => 
            array (
                'permission_id' => 625,
                'role_id' => 45,
            ),
            114 => 
            array (
                'permission_id' => 625,
                'role_id' => 46,
            ),
            115 => 
            array (
                'permission_id' => 625,
                'role_id' => 47,
            ),
            116 => 
            array (
                'permission_id' => 626,
                'role_id' => 10,
            ),
            117 => 
            array (
                'permission_id' => 626,
                'role_id' => 45,
            ),
            118 => 
            array (
                'permission_id' => 626,
                'role_id' => 47,
            ),
            119 => 
            array (
                'permission_id' => 627,
                'role_id' => 10,
            ),
            120 => 
            array (
                'permission_id' => 627,
                'role_id' => 45,
            ),
            121 => 
            array (
                'permission_id' => 627,
                'role_id' => 46,
            ),
            122 => 
            array (
                'permission_id' => 627,
                'role_id' => 47,
            ),
            123 => 
            array (
                'permission_id' => 628,
                'role_id' => 10,
            ),
            124 => 
            array (
                'permission_id' => 628,
                'role_id' => 45,
            ),
            125 => 
            array (
                'permission_id' => 628,
                'role_id' => 46,
            ),
            126 => 
            array (
                'permission_id' => 628,
                'role_id' => 47,
            ),
            127 => 
            array (
                'permission_id' => 629,
                'role_id' => 10,
            ),
            128 => 
            array (
                'permission_id' => 629,
                'role_id' => 45,
            ),
            129 => 
            array (
                'permission_id' => 629,
                'role_id' => 46,
            ),
            130 => 
            array (
                'permission_id' => 629,
                'role_id' => 47,
            ),
            131 => 
            array (
                'permission_id' => 630,
                'role_id' => 10,
            ),
            132 => 
            array (
                'permission_id' => 630,
                'role_id' => 45,
            ),
            133 => 
            array (
                'permission_id' => 630,
                'role_id' => 47,
            ),
            134 => 
            array (
                'permission_id' => 631,
                'role_id' => 10,
            ),
            135 => 
            array (
                'permission_id' => 631,
                'role_id' => 45,
            ),
            136 => 
            array (
                'permission_id' => 631,
                'role_id' => 46,
            ),
            137 => 
            array (
                'permission_id' => 631,
                'role_id' => 47,
            ),
            138 => 
            array (
                'permission_id' => 632,
                'role_id' => 10,
            ),
            139 => 
            array (
                'permission_id' => 632,
                'role_id' => 45,
            ),
            140 => 
            array (
                'permission_id' => 632,
                'role_id' => 46,
            ),
            141 => 
            array (
                'permission_id' => 632,
                'role_id' => 47,
            ),
            142 => 
            array (
                'permission_id' => 633,
                'role_id' => 10,
            ),
            143 => 
            array (
                'permission_id' => 633,
                'role_id' => 45,
            ),
            144 => 
            array (
                'permission_id' => 633,
                'role_id' => 46,
            ),
            145 => 
            array (
                'permission_id' => 633,
                'role_id' => 47,
            ),
            146 => 
            array (
                'permission_id' => 634,
                'role_id' => 10,
            ),
            147 => 
            array (
                'permission_id' => 634,
                'role_id' => 45,
            ),
            148 => 
            array (
                'permission_id' => 634,
                'role_id' => 47,
            ),
            149 => 
            array (
                'permission_id' => 635,
                'role_id' => 10,
            ),
            150 => 
            array (
                'permission_id' => 635,
                'role_id' => 45,
            ),
            151 => 
            array (
                'permission_id' => 635,
                'role_id' => 47,
            ),
            152 => 
            array (
                'permission_id' => 636,
                'role_id' => 10,
            ),
            153 => 
            array (
                'permission_id' => 636,
                'role_id' => 45,
            ),
            154 => 
            array (
                'permission_id' => 636,
                'role_id' => 47,
            ),
            155 => 
            array (
                'permission_id' => 637,
                'role_id' => 10,
            ),
            156 => 
            array (
                'permission_id' => 637,
                'role_id' => 45,
            ),
            157 => 
            array (
                'permission_id' => 637,
                'role_id' => 47,
            ),
            158 => 
            array (
                'permission_id' => 638,
                'role_id' => 10,
            ),
            159 => 
            array (
                'permission_id' => 638,
                'role_id' => 45,
            ),
            160 => 
            array (
                'permission_id' => 638,
                'role_id' => 47,
            ),
            161 => 
            array (
                'permission_id' => 639,
                'role_id' => 10,
            ),
            162 => 
            array (
                'permission_id' => 639,
                'role_id' => 45,
            ),
            163 => 
            array (
                'permission_id' => 639,
                'role_id' => 47,
            ),
            164 => 
            array (
                'permission_id' => 640,
                'role_id' => 10,
            ),
            165 => 
            array (
                'permission_id' => 640,
                'role_id' => 45,
            ),
            166 => 
            array (
                'permission_id' => 640,
                'role_id' => 46,
            ),
            167 => 
            array (
                'permission_id' => 640,
                'role_id' => 47,
            ),
            168 => 
            array (
                'permission_id' => 641,
                'role_id' => 10,
            ),
            169 => 
            array (
                'permission_id' => 641,
                'role_id' => 45,
            ),
            170 => 
            array (
                'permission_id' => 641,
                'role_id' => 46,
            ),
            171 => 
            array (
                'permission_id' => 641,
                'role_id' => 47,
            ),
            172 => 
            array (
                'permission_id' => 642,
                'role_id' => 10,
            ),
            173 => 
            array (
                'permission_id' => 642,
                'role_id' => 45,
            ),
            174 => 
            array (
                'permission_id' => 642,
                'role_id' => 46,
            ),
            175 => 
            array (
                'permission_id' => 642,
                'role_id' => 47,
            ),
            176 => 
            array (
                'permission_id' => 643,
                'role_id' => 10,
            ),
            177 => 
            array (
                'permission_id' => 643,
                'role_id' => 45,
            ),
            178 => 
            array (
                'permission_id' => 643,
                'role_id' => 47,
            ),
            179 => 
            array (
                'permission_id' => 644,
                'role_id' => 10,
            ),
            180 => 
            array (
                'permission_id' => 644,
                'role_id' => 45,
            ),
            181 => 
            array (
                'permission_id' => 644,
                'role_id' => 47,
            ),
            182 => 
            array (
                'permission_id' => 645,
                'role_id' => 10,
            ),
            183 => 
            array (
                'permission_id' => 645,
                'role_id' => 45,
            ),
            184 => 
            array (
                'permission_id' => 645,
                'role_id' => 47,
            ),
            185 => 
            array (
                'permission_id' => 646,
                'role_id' => 10,
            ),
            186 => 
            array (
                'permission_id' => 646,
                'role_id' => 38,
            ),
            187 => 
            array (
                'permission_id' => 646,
                'role_id' => 39,
            ),
            188 => 
            array (
                'permission_id' => 646,
                'role_id' => 40,
            ),
            189 => 
            array (
                'permission_id' => 646,
                'role_id' => 41,
            ),
            190 => 
            array (
                'permission_id' => 646,
                'role_id' => 43,
            ),
            191 => 
            array (
                'permission_id' => 646,
                'role_id' => 47,
            ),
            192 => 
            array (
                'permission_id' => 646,
                'role_id' => 48,
            ),
            193 => 
            array (
                'permission_id' => 647,
                'role_id' => 10,
            ),
            194 => 
            array (
                'permission_id' => 647,
                'role_id' => 43,
            ),
            195 => 
            array (
                'permission_id' => 647,
                'role_id' => 47,
            ),
            196 => 
            array (
                'permission_id' => 648,
                'role_id' => 10,
            ),
            197 => 
            array (
                'permission_id' => 648,
                'role_id' => 43,
            ),
            198 => 
            array (
                'permission_id' => 648,
                'role_id' => 47,
            ),
            199 => 
            array (
                'permission_id' => 649,
                'role_id' => 10,
            ),
            200 => 
            array (
                'permission_id' => 649,
                'role_id' => 43,
            ),
            201 => 
            array (
                'permission_id' => 649,
                'role_id' => 47,
            ),
            202 => 
            array (
                'permission_id' => 650,
                'role_id' => 10,
            ),
            203 => 
            array (
                'permission_id' => 650,
                'role_id' => 43,
            ),
            204 => 
            array (
                'permission_id' => 650,
                'role_id' => 47,
            ),
            205 => 
            array (
                'permission_id' => 651,
                'role_id' => 10,
            ),
            206 => 
            array (
                'permission_id' => 651,
                'role_id' => 43,
            ),
            207 => 
            array (
                'permission_id' => 651,
                'role_id' => 47,
            ),
            208 => 
            array (
                'permission_id' => 652,
                'role_id' => 10,
            ),
            209 => 
            array (
                'permission_id' => 652,
                'role_id' => 43,
            ),
            210 => 
            array (
                'permission_id' => 652,
                'role_id' => 47,
            ),
            211 => 
            array (
                'permission_id' => 653,
                'role_id' => 10,
            ),
            212 => 
            array (
                'permission_id' => 653,
                'role_id' => 43,
            ),
            213 => 
            array (
                'permission_id' => 653,
                'role_id' => 47,
            ),
            214 => 
            array (
                'permission_id' => 654,
                'role_id' => 10,
            ),
            215 => 
            array (
                'permission_id' => 654,
                'role_id' => 38,
            ),
            216 => 
            array (
                'permission_id' => 654,
                'role_id' => 39,
            ),
            217 => 
            array (
                'permission_id' => 654,
                'role_id' => 40,
            ),
            218 => 
            array (
                'permission_id' => 654,
                'role_id' => 41,
            ),
            219 => 
            array (
                'permission_id' => 654,
                'role_id' => 47,
            ),
            220 => 
            array (
                'permission_id' => 655,
                'role_id' => 10,
            ),
            221 => 
            array (
                'permission_id' => 655,
                'role_id' => 38,
            ),
            222 => 
            array (
                'permission_id' => 655,
                'role_id' => 40,
            ),
            223 => 
            array (
                'permission_id' => 655,
                'role_id' => 41,
            ),
            224 => 
            array (
                'permission_id' => 655,
                'role_id' => 47,
            ),
            225 => 
            array (
                'permission_id' => 656,
                'role_id' => 10,
            ),
            226 => 
            array (
                'permission_id' => 656,
                'role_id' => 38,
            ),
            227 => 
            array (
                'permission_id' => 656,
                'role_id' => 40,
            ),
            228 => 
            array (
                'permission_id' => 656,
                'role_id' => 41,
            ),
            229 => 
            array (
                'permission_id' => 656,
                'role_id' => 47,
            ),
            230 => 
            array (
                'permission_id' => 657,
                'role_id' => 10,
            ),
            231 => 
            array (
                'permission_id' => 657,
                'role_id' => 40,
            ),
            232 => 
            array (
                'permission_id' => 657,
                'role_id' => 41,
            ),
            233 => 
            array (
                'permission_id' => 657,
                'role_id' => 47,
            ),
            234 => 
            array (
                'permission_id' => 658,
                'role_id' => 10,
            ),
            235 => 
            array (
                'permission_id' => 658,
                'role_id' => 39,
            ),
            236 => 
            array (
                'permission_id' => 658,
                'role_id' => 40,
            ),
            237 => 
            array (
                'permission_id' => 658,
                'role_id' => 47,
            ),
            238 => 
            array (
                'permission_id' => 659,
                'role_id' => 10,
            ),
            239 => 
            array (
                'permission_id' => 659,
                'role_id' => 39,
            ),
            240 => 
            array (
                'permission_id' => 659,
                'role_id' => 40,
            ),
            241 => 
            array (
                'permission_id' => 659,
                'role_id' => 47,
            ),
            242 => 
            array (
                'permission_id' => 660,
                'role_id' => 10,
            ),
            243 => 
            array (
                'permission_id' => 660,
                'role_id' => 39,
            ),
            244 => 
            array (
                'permission_id' => 660,
                'role_id' => 40,
            ),
            245 => 
            array (
                'permission_id' => 660,
                'role_id' => 47,
            ),
            246 => 
            array (
                'permission_id' => 661,
                'role_id' => 10,
            ),
            247 => 
            array (
                'permission_id' => 661,
                'role_id' => 39,
            ),
            248 => 
            array (
                'permission_id' => 661,
                'role_id' => 40,
            ),
            249 => 
            array (
                'permission_id' => 661,
                'role_id' => 47,
            ),
            250 => 
            array (
                'permission_id' => 662,
                'role_id' => 10,
            ),
            251 => 
            array (
                'permission_id' => 662,
                'role_id' => 38,
            ),
            252 => 
            array (
                'permission_id' => 662,
                'role_id' => 39,
            ),
            253 => 
            array (
                'permission_id' => 662,
                'role_id' => 40,
            ),
            254 => 
            array (
                'permission_id' => 662,
                'role_id' => 41,
            ),
            255 => 
            array (
                'permission_id' => 662,
                'role_id' => 43,
            ),
            256 => 
            array (
                'permission_id' => 662,
                'role_id' => 44,
            ),
            257 => 
            array (
                'permission_id' => 662,
                'role_id' => 47,
            ),
            258 => 
            array (
                'permission_id' => 663,
                'role_id' => 10,
            ),
            259 => 
            array (
                'permission_id' => 663,
                'role_id' => 40,
            ),
            260 => 
            array (
                'permission_id' => 663,
                'role_id' => 41,
            ),
            261 => 
            array (
                'permission_id' => 663,
                'role_id' => 47,
            ),
            262 => 
            array (
                'permission_id' => 664,
                'role_id' => 10,
            ),
            263 => 
            array (
                'permission_id' => 664,
                'role_id' => 40,
            ),
            264 => 
            array (
                'permission_id' => 664,
                'role_id' => 41,
            ),
            265 => 
            array (
                'permission_id' => 664,
                'role_id' => 47,
            ),
            266 => 
            array (
                'permission_id' => 665,
                'role_id' => 10,
            ),
            267 => 
            array (
                'permission_id' => 665,
                'role_id' => 40,
            ),
            268 => 
            array (
                'permission_id' => 665,
                'role_id' => 47,
            ),
            269 => 
            array (
                'permission_id' => 666,
                'role_id' => 10,
            ),
            270 => 
            array (
                'permission_id' => 666,
                'role_id' => 39,
            ),
            271 => 
            array (
                'permission_id' => 666,
                'role_id' => 40,
            ),
            272 => 
            array (
                'permission_id' => 666,
                'role_id' => 47,
            ),
            273 => 
            array (
                'permission_id' => 667,
                'role_id' => 10,
            ),
            274 => 
            array (
                'permission_id' => 667,
                'role_id' => 39,
            ),
            275 => 
            array (
                'permission_id' => 667,
                'role_id' => 40,
            ),
            276 => 
            array (
                'permission_id' => 667,
                'role_id' => 47,
            ),
            277 => 
            array (
                'permission_id' => 668,
                'role_id' => 10,
            ),
            278 => 
            array (
                'permission_id' => 668,
                'role_id' => 39,
            ),
            279 => 
            array (
                'permission_id' => 668,
                'role_id' => 40,
            ),
            280 => 
            array (
                'permission_id' => 668,
                'role_id' => 47,
            ),
            281 => 
            array (
                'permission_id' => 669,
                'role_id' => 10,
            ),
            282 => 
            array (
                'permission_id' => 669,
                'role_id' => 39,
            ),
            283 => 
            array (
                'permission_id' => 669,
                'role_id' => 40,
            ),
            284 => 
            array (
                'permission_id' => 669,
                'role_id' => 47,
            ),
            285 => 
            array (
                'permission_id' => 670,
                'role_id' => 10,
            ),
            286 => 
            array (
                'permission_id' => 670,
                'role_id' => 38,
            ),
            287 => 
            array (
                'permission_id' => 670,
                'role_id' => 39,
            ),
            288 => 
            array (
                'permission_id' => 670,
                'role_id' => 40,
            ),
            289 => 
            array (
                'permission_id' => 670,
                'role_id' => 41,
            ),
            290 => 
            array (
                'permission_id' => 670,
                'role_id' => 47,
            ),
            291 => 
            array (
                'permission_id' => 671,
                'role_id' => 10,
            ),
            292 => 
            array (
                'permission_id' => 671,
                'role_id' => 40,
            ),
            293 => 
            array (
                'permission_id' => 671,
                'role_id' => 41,
            ),
            294 => 
            array (
                'permission_id' => 671,
                'role_id' => 47,
            ),
            295 => 
            array (
                'permission_id' => 672,
                'role_id' => 10,
            ),
            296 => 
            array (
                'permission_id' => 672,
                'role_id' => 40,
            ),
            297 => 
            array (
                'permission_id' => 672,
                'role_id' => 41,
            ),
            298 => 
            array (
                'permission_id' => 672,
                'role_id' => 47,
            ),
            299 => 
            array (
                'permission_id' => 673,
                'role_id' => 10,
            ),
            300 => 
            array (
                'permission_id' => 673,
                'role_id' => 40,
            ),
            301 => 
            array (
                'permission_id' => 673,
                'role_id' => 47,
            ),
            302 => 
            array (
                'permission_id' => 674,
                'role_id' => 10,
            ),
            303 => 
            array (
                'permission_id' => 674,
                'role_id' => 39,
            ),
            304 => 
            array (
                'permission_id' => 674,
                'role_id' => 40,
            ),
            305 => 
            array (
                'permission_id' => 674,
                'role_id' => 47,
            ),
            306 => 
            array (
                'permission_id' => 675,
                'role_id' => 10,
            ),
            307 => 
            array (
                'permission_id' => 675,
                'role_id' => 39,
            ),
            308 => 
            array (
                'permission_id' => 675,
                'role_id' => 40,
            ),
            309 => 
            array (
                'permission_id' => 675,
                'role_id' => 47,
            ),
            310 => 
            array (
                'permission_id' => 676,
                'role_id' => 10,
            ),
            311 => 
            array (
                'permission_id' => 676,
                'role_id' => 39,
            ),
            312 => 
            array (
                'permission_id' => 676,
                'role_id' => 40,
            ),
            313 => 
            array (
                'permission_id' => 676,
                'role_id' => 47,
            ),
            314 => 
            array (
                'permission_id' => 677,
                'role_id' => 10,
            ),
            315 => 
            array (
                'permission_id' => 677,
                'role_id' => 39,
            ),
            316 => 
            array (
                'permission_id' => 677,
                'role_id' => 40,
            ),
            317 => 
            array (
                'permission_id' => 677,
                'role_id' => 47,
            ),
            318 => 
            array (
                'permission_id' => 678,
                'role_id' => 10,
            ),
            319 => 
            array (
                'permission_id' => 678,
                'role_id' => 39,
            ),
            320 => 
            array (
                'permission_id' => 678,
                'role_id' => 40,
            ),
            321 => 
            array (
                'permission_id' => 678,
                'role_id' => 47,
            ),
            322 => 
            array (
                'permission_id' => 681,
                'role_id' => 10,
            ),
            323 => 
            array (
                'permission_id' => 681,
                'role_id' => 35,
            ),
            324 => 
            array (
                'permission_id' => 681,
                'role_id' => 40,
            ),
            325 => 
            array (
                'permission_id' => 681,
                'role_id' => 41,
            ),
            326 => 
            array (
                'permission_id' => 681,
                'role_id' => 42,
            ),
            327 => 
            array (
                'permission_id' => 681,
                'role_id' => 47,
            ),
            328 => 
            array (
                'permission_id' => 682,
                'role_id' => 10,
            ),
            329 => 
            array (
                'permission_id' => 682,
                'role_id' => 35,
            ),
            330 => 
            array (
                'permission_id' => 682,
                'role_id' => 42,
            ),
            331 => 
            array (
                'permission_id' => 682,
                'role_id' => 47,
            ),
            332 => 
            array (
                'permission_id' => 683,
                'role_id' => 10,
            ),
            333 => 
            array (
                'permission_id' => 683,
                'role_id' => 35,
            ),
            334 => 
            array (
                'permission_id' => 683,
                'role_id' => 42,
            ),
            335 => 
            array (
                'permission_id' => 683,
                'role_id' => 47,
            ),
            336 => 
            array (
                'permission_id' => 684,
                'role_id' => 10,
            ),
            337 => 
            array (
                'permission_id' => 684,
                'role_id' => 35,
            ),
            338 => 
            array (
                'permission_id' => 684,
                'role_id' => 42,
            ),
            339 => 
            array (
                'permission_id' => 684,
                'role_id' => 47,
            ),
            340 => 
            array (
                'permission_id' => 685,
                'role_id' => 10,
            ),
            341 => 
            array (
                'permission_id' => 685,
                'role_id' => 35,
            ),
            342 => 
            array (
                'permission_id' => 685,
                'role_id' => 47,
            ),
            343 => 
            array (
                'permission_id' => 686,
                'role_id' => 10,
            ),
            344 => 
            array (
                'permission_id' => 686,
                'role_id' => 35,
            ),
            345 => 
            array (
                'permission_id' => 686,
                'role_id' => 47,
            ),
            346 => 
            array (
                'permission_id' => 687,
                'role_id' => 10,
            ),
            347 => 
            array (
                'permission_id' => 687,
                'role_id' => 35,
            ),
            348 => 
            array (
                'permission_id' => 687,
                'role_id' => 47,
            ),
            349 => 
            array (
                'permission_id' => 688,
                'role_id' => 10,
            ),
            350 => 
            array (
                'permission_id' => 688,
                'role_id' => 35,
            ),
            351 => 
            array (
                'permission_id' => 688,
                'role_id' => 47,
            ),
            352 => 
            array (
                'permission_id' => 689,
                'role_id' => 10,
            ),
            353 => 
            array (
                'permission_id' => 689,
                'role_id' => 37,
            ),
            354 => 
            array (
                'permission_id' => 689,
                'role_id' => 38,
            ),
            355 => 
            array (
                'permission_id' => 689,
                'role_id' => 39,
            ),
            356 => 
            array (
                'permission_id' => 689,
                'role_id' => 40,
            ),
            357 => 
            array (
                'permission_id' => 689,
                'role_id' => 41,
            ),
            358 => 
            array (
                'permission_id' => 689,
                'role_id' => 47,
            ),
            359 => 
            array (
                'permission_id' => 690,
                'role_id' => 10,
            ),
            360 => 
            array (
                'permission_id' => 690,
                'role_id' => 37,
            ),
            361 => 
            array (
                'permission_id' => 690,
                'role_id' => 38,
            ),
            362 => 
            array (
                'permission_id' => 690,
                'role_id' => 47,
            ),
            363 => 
            array (
                'permission_id' => 691,
                'role_id' => 10,
            ),
            364 => 
            array (
                'permission_id' => 691,
                'role_id' => 37,
            ),
            365 => 
            array (
                'permission_id' => 691,
                'role_id' => 38,
            ),
            366 => 
            array (
                'permission_id' => 691,
                'role_id' => 47,
            ),
            367 => 
            array (
                'permission_id' => 692,
                'role_id' => 10,
            ),
            368 => 
            array (
                'permission_id' => 692,
                'role_id' => 37,
            ),
            369 => 
            array (
                'permission_id' => 692,
                'role_id' => 38,
            ),
            370 => 
            array (
                'permission_id' => 692,
                'role_id' => 47,
            ),
            371 => 
            array (
                'permission_id' => 693,
                'role_id' => 10,
            ),
            372 => 
            array (
                'permission_id' => 693,
                'role_id' => 37,
            ),
            373 => 
            array (
                'permission_id' => 693,
                'role_id' => 38,
            ),
            374 => 
            array (
                'permission_id' => 693,
                'role_id' => 39,
            ),
            375 => 
            array (
                'permission_id' => 693,
                'role_id' => 47,
            ),
            376 => 
            array (
                'permission_id' => 694,
                'role_id' => 10,
            ),
            377 => 
            array (
                'permission_id' => 694,
                'role_id' => 37,
            ),
            378 => 
            array (
                'permission_id' => 694,
                'role_id' => 38,
            ),
            379 => 
            array (
                'permission_id' => 694,
                'role_id' => 47,
            ),
            380 => 
            array (
                'permission_id' => 695,
                'role_id' => 10,
            ),
            381 => 
            array (
                'permission_id' => 695,
                'role_id' => 45,
            ),
            382 => 
            array (
                'permission_id' => 695,
                'role_id' => 46,
            ),
            383 => 
            array (
                'permission_id' => 695,
                'role_id' => 47,
            ),
            384 => 
            array (
                'permission_id' => 696,
                'role_id' => 10,
            ),
            385 => 
            array (
                'permission_id' => 696,
                'role_id' => 45,
            ),
            386 => 
            array (
                'permission_id' => 696,
                'role_id' => 46,
            ),
            387 => 
            array (
                'permission_id' => 696,
                'role_id' => 47,
            ),
            388 => 
            array (
                'permission_id' => 697,
                'role_id' => 10,
            ),
            389 => 
            array (
                'permission_id' => 697,
                'role_id' => 45,
            ),
            390 => 
            array (
                'permission_id' => 697,
                'role_id' => 46,
            ),
            391 => 
            array (
                'permission_id' => 697,
                'role_id' => 47,
            ),
            392 => 
            array (
                'permission_id' => 698,
                'role_id' => 10,
            ),
            393 => 
            array (
                'permission_id' => 698,
                'role_id' => 45,
            ),
            394 => 
            array (
                'permission_id' => 698,
                'role_id' => 47,
            ),
            395 => 
            array (
                'permission_id' => 699,
                'role_id' => 10,
            ),
            396 => 
            array (
                'permission_id' => 699,
                'role_id' => 35,
            ),
            397 => 
            array (
                'permission_id' => 699,
                'role_id' => 37,
            ),
            398 => 
            array (
                'permission_id' => 699,
                'role_id' => 38,
            ),
            399 => 
            array (
                'permission_id' => 699,
                'role_id' => 39,
            ),
            400 => 
            array (
                'permission_id' => 699,
                'role_id' => 40,
            ),
            401 => 
            array (
                'permission_id' => 699,
                'role_id' => 41,
            ),
            402 => 
            array (
                'permission_id' => 699,
                'role_id' => 42,
            ),
            403 => 
            array (
                'permission_id' => 699,
                'role_id' => 43,
            ),
            404 => 
            array (
                'permission_id' => 699,
                'role_id' => 45,
            ),
            405 => 
            array (
                'permission_id' => 699,
                'role_id' => 47,
            ),
            406 => 
            array (
                'permission_id' => 700,
                'role_id' => 10,
            ),
            407 => 
            array (
                'permission_id' => 700,
                'role_id' => 37,
            ),
            408 => 
            array (
                'permission_id' => 700,
                'role_id' => 38,
            ),
            409 => 
            array (
                'permission_id' => 700,
                'role_id' => 47,
            ),
            410 => 
            array (
                'permission_id' => 701,
                'role_id' => 10,
            ),
            411 => 
            array (
                'permission_id' => 701,
                'role_id' => 37,
            ),
            412 => 
            array (
                'permission_id' => 701,
                'role_id' => 38,
            ),
            413 => 
            array (
                'permission_id' => 701,
                'role_id' => 47,
            ),
            414 => 
            array (
                'permission_id' => 702,
                'role_id' => 10,
            ),
            415 => 
            array (
                'permission_id' => 702,
                'role_id' => 37,
            ),
            416 => 
            array (
                'permission_id' => 702,
                'role_id' => 38,
            ),
            417 => 
            array (
                'permission_id' => 702,
                'role_id' => 47,
            ),
            418 => 
            array (
                'permission_id' => 703,
                'role_id' => 10,
            ),
            419 => 
            array (
                'permission_id' => 703,
                'role_id' => 35,
            ),
            420 => 
            array (
                'permission_id' => 703,
                'role_id' => 37,
            ),
            421 => 
            array (
                'permission_id' => 703,
                'role_id' => 38,
            ),
            422 => 
            array (
                'permission_id' => 703,
                'role_id' => 39,
            ),
            423 => 
            array (
                'permission_id' => 703,
                'role_id' => 41,
            ),
            424 => 
            array (
                'permission_id' => 703,
                'role_id' => 42,
            ),
            425 => 
            array (
                'permission_id' => 703,
                'role_id' => 45,
            ),
            426 => 
            array (
                'permission_id' => 703,
                'role_id' => 47,
            ),
            427 => 
            array (
                'permission_id' => 704,
                'role_id' => 10,
            ),
            428 => 
            array (
                'permission_id' => 704,
                'role_id' => 37,
            ),
            429 => 
            array (
                'permission_id' => 704,
                'role_id' => 38,
            ),
            430 => 
            array (
                'permission_id' => 704,
                'role_id' => 47,
            ),
            431 => 
            array (
                'permission_id' => 705,
                'role_id' => 10,
            ),
            432 => 
            array (
                'permission_id' => 705,
                'role_id' => 37,
            ),
            433 => 
            array (
                'permission_id' => 705,
                'role_id' => 38,
            ),
            434 => 
            array (
                'permission_id' => 705,
                'role_id' => 47,
            ),
            435 => 
            array (
                'permission_id' => 706,
                'role_id' => 10,
            ),
            436 => 
            array (
                'permission_id' => 706,
                'role_id' => 37,
            ),
            437 => 
            array (
                'permission_id' => 706,
                'role_id' => 38,
            ),
            438 => 
            array (
                'permission_id' => 706,
                'role_id' => 47,
            ),
            439 => 
            array (
                'permission_id' => 707,
                'role_id' => 10,
            ),
            440 => 
            array (
                'permission_id' => 707,
                'role_id' => 43,
            ),
            441 => 
            array (
                'permission_id' => 707,
                'role_id' => 44,
            ),
            442 => 
            array (
                'permission_id' => 707,
                'role_id' => 47,
            ),
            443 => 
            array (
                'permission_id' => 708,
                'role_id' => 10,
            ),
            444 => 
            array (
                'permission_id' => 708,
                'role_id' => 37,
            ),
            445 => 
            array (
                'permission_id' => 708,
                'role_id' => 38,
            ),
            446 => 
            array (
                'permission_id' => 708,
                'role_id' => 39,
            ),
            447 => 
            array (
                'permission_id' => 708,
                'role_id' => 40,
            ),
            448 => 
            array (
                'permission_id' => 708,
                'role_id' => 41,
            ),
            449 => 
            array (
                'permission_id' => 708,
                'role_id' => 47,
            ),
            450 => 
            array (
                'permission_id' => 709,
                'role_id' => 10,
            ),
            451 => 
            array (
                'permission_id' => 709,
                'role_id' => 37,
            ),
            452 => 
            array (
                'permission_id' => 709,
                'role_id' => 38,
            ),
            453 => 
            array (
                'permission_id' => 709,
                'role_id' => 47,
            ),
            454 => 
            array (
                'permission_id' => 710,
                'role_id' => 45,
            ),
            455 => 
            array (
                'permission_id' => 710,
                'role_id' => 46,
            ),
            456 => 
            array (
                'permission_id' => 710,
                'role_id' => 47,
            ),
            457 => 
            array (
                'permission_id' => 711,
                'role_id' => 45,
            ),
            458 => 
            array (
                'permission_id' => 711,
                'role_id' => 46,
            ),
            459 => 
            array (
                'permission_id' => 711,
                'role_id' => 47,
            ),
            460 => 
            array (
                'permission_id' => 712,
                'role_id' => 45,
            ),
            461 => 
            array (
                'permission_id' => 712,
                'role_id' => 47,
            ),
            462 => 
            array (
                'permission_id' => 714,
                'role_id' => 39,
            ),
            463 => 
            array (
                'permission_id' => 714,
                'role_id' => 40,
            ),
            464 => 
            array (
                'permission_id' => 714,
                'role_id' => 41,
            ),
            465 => 
            array (
                'permission_id' => 714,
                'role_id' => 42,
            ),
            466 => 
            array (
                'permission_id' => 714,
                'role_id' => 45,
            ),
            467 => 
            array (
                'permission_id' => 714,
                'role_id' => 46,
            ),
            468 => 
            array (
                'permission_id' => 714,
                'role_id' => 47,
            ),
            469 => 
            array (
                'permission_id' => 715,
                'role_id' => 39,
            ),
            470 => 
            array (
                'permission_id' => 715,
                'role_id' => 40,
            ),
            471 => 
            array (
                'permission_id' => 715,
                'role_id' => 41,
            ),
            472 => 
            array (
                'permission_id' => 715,
                'role_id' => 42,
            ),
            473 => 
            array (
                'permission_id' => 715,
                'role_id' => 45,
            ),
            474 => 
            array (
                'permission_id' => 715,
                'role_id' => 46,
            ),
            475 => 
            array (
                'permission_id' => 715,
                'role_id' => 47,
            ),
            476 => 
            array (
                'permission_id' => 716,
                'role_id' => 45,
            ),
            477 => 
            array (
                'permission_id' => 716,
                'role_id' => 46,
            ),
            478 => 
            array (
                'permission_id' => 716,
                'role_id' => 47,
            ),
            479 => 
            array (
                'permission_id' => 717,
                'role_id' => 45,
            ),
            480 => 
            array (
                'permission_id' => 717,
                'role_id' => 46,
            ),
            481 => 
            array (
                'permission_id' => 717,
                'role_id' => 47,
            ),
            482 => 
            array (
                'permission_id' => 718,
                'role_id' => 45,
            ),
            483 => 
            array (
                'permission_id' => 718,
                'role_id' => 47,
            ),
            484 => 
            array (
                'permission_id' => 719,
                'role_id' => 45,
            ),
            485 => 
            array (
                'permission_id' => 719,
                'role_id' => 46,
            ),
            486 => 
            array (
                'permission_id' => 719,
                'role_id' => 47,
            ),
            487 => 
            array (
                'permission_id' => 720,
                'role_id' => 45,
            ),
            488 => 
            array (
                'permission_id' => 720,
                'role_id' => 46,
            ),
            489 => 
            array (
                'permission_id' => 720,
                'role_id' => 47,
            ),
            490 => 
            array (
                'permission_id' => 721,
                'role_id' => 45,
            ),
            491 => 
            array (
                'permission_id' => 721,
                'role_id' => 46,
            ),
            492 => 
            array (
                'permission_id' => 721,
                'role_id' => 47,
            ),
            493 => 
            array (
                'permission_id' => 722,
                'role_id' => 45,
            ),
            494 => 
            array (
                'permission_id' => 722,
                'role_id' => 47,
            ),
            495 => 
            array (
                'permission_id' => 723,
                'role_id' => 39,
            ),
            496 => 
            array (
                'permission_id' => 723,
                'role_id' => 40,
            ),
            497 => 
            array (
                'permission_id' => 723,
                'role_id' => 41,
            ),
            498 => 
            array (
                'permission_id' => 723,
                'role_id' => 42,
            ),
            499 => 
            array (
                'permission_id' => 723,
                'role_id' => 45,
            ),
        ));
        \DB::table('role_has_permissions')->insert(array (
            0 => 
            array (
                'permission_id' => 723,
                'role_id' => 46,
            ),
            1 => 
            array (
                'permission_id' => 723,
                'role_id' => 47,
            ),
            2 => 
            array (
                'permission_id' => 724,
                'role_id' => 45,
            ),
            3 => 
            array (
                'permission_id' => 724,
                'role_id' => 46,
            ),
            4 => 
            array (
                'permission_id' => 724,
                'role_id' => 47,
            ),
            5 => 
            array (
                'permission_id' => 725,
                'role_id' => 45,
            ),
            6 => 
            array (
                'permission_id' => 725,
                'role_id' => 46,
            ),
            7 => 
            array (
                'permission_id' => 725,
                'role_id' => 47,
            ),
            8 => 
            array (
                'permission_id' => 726,
                'role_id' => 45,
            ),
            9 => 
            array (
                'permission_id' => 726,
                'role_id' => 47,
            ),
            10 => 
            array (
                'permission_id' => 727,
                'role_id' => 35,
            ),
            11 => 
            array (
                'permission_id' => 727,
                'role_id' => 37,
            ),
            12 => 
            array (
                'permission_id' => 727,
                'role_id' => 39,
            ),
            13 => 
            array (
                'permission_id' => 727,
                'role_id' => 40,
            ),
            14 => 
            array (
                'permission_id' => 727,
                'role_id' => 43,
            ),
            15 => 
            array (
                'permission_id' => 727,
                'role_id' => 45,
            ),
            16 => 
            array (
                'permission_id' => 727,
                'role_id' => 47,
            ),
            17 => 
            array (
                'permission_id' => 727,
                'role_id' => 48,
            ),
            18 => 
            array (
                'permission_id' => 728,
                'role_id' => 35,
            ),
            19 => 
            array (
                'permission_id' => 728,
                'role_id' => 37,
            ),
            20 => 
            array (
                'permission_id' => 728,
                'role_id' => 39,
            ),
            21 => 
            array (
                'permission_id' => 728,
                'role_id' => 40,
            ),
            22 => 
            array (
                'permission_id' => 728,
                'role_id' => 43,
            ),
            23 => 
            array (
                'permission_id' => 728,
                'role_id' => 45,
            ),
            24 => 
            array (
                'permission_id' => 728,
                'role_id' => 47,
            ),
            25 => 
            array (
                'permission_id' => 728,
                'role_id' => 48,
            ),
            26 => 
            array (
                'permission_id' => 729,
                'role_id' => 35,
            ),
            27 => 
            array (
                'permission_id' => 729,
                'role_id' => 37,
            ),
            28 => 
            array (
                'permission_id' => 729,
                'role_id' => 39,
            ),
            29 => 
            array (
                'permission_id' => 729,
                'role_id' => 40,
            ),
            30 => 
            array (
                'permission_id' => 729,
                'role_id' => 43,
            ),
            31 => 
            array (
                'permission_id' => 729,
                'role_id' => 45,
            ),
            32 => 
            array (
                'permission_id' => 729,
                'role_id' => 47,
            ),
            33 => 
            array (
                'permission_id' => 729,
                'role_id' => 48,
            ),
            34 => 
            array (
                'permission_id' => 730,
                'role_id' => 35,
            ),
            35 => 
            array (
                'permission_id' => 730,
                'role_id' => 37,
            ),
            36 => 
            array (
                'permission_id' => 730,
                'role_id' => 39,
            ),
            37 => 
            array (
                'permission_id' => 730,
                'role_id' => 40,
            ),
            38 => 
            array (
                'permission_id' => 730,
                'role_id' => 43,
            ),
            39 => 
            array (
                'permission_id' => 730,
                'role_id' => 45,
            ),
            40 => 
            array (
                'permission_id' => 730,
                'role_id' => 47,
            ),
            41 => 
            array (
                'permission_id' => 730,
                'role_id' => 48,
            ),
            42 => 
            array (
                'permission_id' => 731,
                'role_id' => 47,
            ),
            43 => 
            array (
                'permission_id' => 732,
                'role_id' => 35,
            ),
            44 => 
            array (
                'permission_id' => 732,
                'role_id' => 38,
            ),
            45 => 
            array (
                'permission_id' => 732,
                'role_id' => 39,
            ),
            46 => 
            array (
                'permission_id' => 732,
                'role_id' => 40,
            ),
            47 => 
            array (
                'permission_id' => 732,
                'role_id' => 43,
            ),
            48 => 
            array (
                'permission_id' => 732,
                'role_id' => 45,
            ),
            49 => 
            array (
                'permission_id' => 732,
                'role_id' => 47,
            ),
            50 => 
            array (
                'permission_id' => 733,
                'role_id' => 39,
            ),
            51 => 
            array (
                'permission_id' => 733,
                'role_id' => 43,
            ),
            52 => 
            array (
                'permission_id' => 733,
                'role_id' => 44,
            ),
            53 => 
            array (
                'permission_id' => 733,
                'role_id' => 47,
            ),
            54 => 
            array (
                'permission_id' => 733,
                'role_id' => 48,
            ),
            55 => 
            array (
                'permission_id' => 734,
                'role_id' => 43,
            ),
            56 => 
            array (
                'permission_id' => 734,
                'role_id' => 44,
            ),
            57 => 
            array (
                'permission_id' => 734,
                'role_id' => 47,
            ),
            58 => 
            array (
                'permission_id' => 734,
                'role_id' => 48,
            ),
            59 => 
            array (
                'permission_id' => 735,
                'role_id' => 43,
            ),
            60 => 
            array (
                'permission_id' => 735,
                'role_id' => 44,
            ),
            61 => 
            array (
                'permission_id' => 735,
                'role_id' => 47,
            ),
            62 => 
            array (
                'permission_id' => 735,
                'role_id' => 48,
            ),
            63 => 
            array (
                'permission_id' => 736,
                'role_id' => 43,
            ),
            64 => 
            array (
                'permission_id' => 736,
                'role_id' => 44,
            ),
            65 => 
            array (
                'permission_id' => 736,
                'role_id' => 47,
            ),
            66 => 
            array (
                'permission_id' => 736,
                'role_id' => 48,
            ),
            67 => 
            array (
                'permission_id' => 737,
                'role_id' => 47,
            ),
            68 => 
            array (
                'permission_id' => 738,
                'role_id' => 47,
            ),
            69 => 
            array (
                'permission_id' => 739,
                'role_id' => 47,
            ),
            70 => 
            array (
                'permission_id' => 740,
                'role_id' => 47,
            ),
            71 => 
            array (
                'permission_id' => 741,
                'role_id' => 35,
            ),
            72 => 
            array (
                'permission_id' => 741,
                'role_id' => 37,
            ),
            73 => 
            array (
                'permission_id' => 741,
                'role_id' => 38,
            ),
            74 => 
            array (
                'permission_id' => 741,
                'role_id' => 39,
            ),
            75 => 
            array (
                'permission_id' => 741,
                'role_id' => 47,
            ),
            76 => 
            array (
                'permission_id' => 742,
                'role_id' => 37,
            ),
            77 => 
            array (
                'permission_id' => 742,
                'role_id' => 38,
            ),
            78 => 
            array (
                'permission_id' => 742,
                'role_id' => 47,
            ),
            79 => 
            array (
                'permission_id' => 743,
                'role_id' => 37,
            ),
            80 => 
            array (
                'permission_id' => 743,
                'role_id' => 38,
            ),
            81 => 
            array (
                'permission_id' => 743,
                'role_id' => 47,
            ),
            82 => 
            array (
                'permission_id' => 744,
                'role_id' => 37,
            ),
            83 => 
            array (
                'permission_id' => 744,
                'role_id' => 38,
            ),
            84 => 
            array (
                'permission_id' => 744,
                'role_id' => 47,
            ),
            85 => 
            array (
                'permission_id' => 745,
                'role_id' => 37,
            ),
            86 => 
            array (
                'permission_id' => 745,
                'role_id' => 38,
            ),
            87 => 
            array (
                'permission_id' => 745,
                'role_id' => 39,
            ),
            88 => 
            array (
                'permission_id' => 745,
                'role_id' => 47,
            ),
            89 => 
            array (
                'permission_id' => 746,
                'role_id' => 37,
            ),
            90 => 
            array (
                'permission_id' => 746,
                'role_id' => 38,
            ),
            91 => 
            array (
                'permission_id' => 746,
                'role_id' => 47,
            ),
            92 => 
            array (
                'permission_id' => 747,
                'role_id' => 37,
            ),
            93 => 
            array (
                'permission_id' => 747,
                'role_id' => 38,
            ),
            94 => 
            array (
                'permission_id' => 747,
                'role_id' => 47,
            ),
            95 => 
            array (
                'permission_id' => 748,
                'role_id' => 37,
            ),
            96 => 
            array (
                'permission_id' => 748,
                'role_id' => 38,
            ),
            97 => 
            array (
                'permission_id' => 748,
                'role_id' => 47,
            ),
            98 => 
            array (
                'permission_id' => 749,
                'role_id' => 35,
            ),
            99 => 
            array (
                'permission_id' => 749,
                'role_id' => 39,
            ),
            100 => 
            array (
                'permission_id' => 749,
                'role_id' => 45,
            ),
            101 => 
            array (
                'permission_id' => 749,
                'role_id' => 47,
            ),
            102 => 
            array (
                'permission_id' => 750,
                'role_id' => 47,
            ),
            103 => 
            array (
                'permission_id' => 751,
                'role_id' => 47,
            ),
            104 => 
            array (
                'permission_id' => 752,
                'role_id' => 47,
            ),
            105 => 
            array (
                'permission_id' => 753,
                'role_id' => 37,
            ),
            106 => 
            array (
                'permission_id' => 753,
                'role_id' => 38,
            ),
            107 => 
            array (
                'permission_id' => 753,
                'role_id' => 39,
            ),
            108 => 
            array (
                'permission_id' => 753,
                'role_id' => 43,
            ),
            109 => 
            array (
                'permission_id' => 753,
                'role_id' => 44,
            ),
            110 => 
            array (
                'permission_id' => 753,
                'role_id' => 47,
            ),
            111 => 
            array (
                'permission_id' => 753,
                'role_id' => 48,
            ),
            112 => 
            array (
                'permission_id' => 754,
                'role_id' => 37,
            ),
            113 => 
            array (
                'permission_id' => 754,
                'role_id' => 38,
            ),
            114 => 
            array (
                'permission_id' => 754,
                'role_id' => 39,
            ),
            115 => 
            array (
                'permission_id' => 754,
                'role_id' => 43,
            ),
            116 => 
            array (
                'permission_id' => 754,
                'role_id' => 44,
            ),
            117 => 
            array (
                'permission_id' => 754,
                'role_id' => 47,
            ),
            118 => 
            array (
                'permission_id' => 754,
                'role_id' => 48,
            ),
            119 => 
            array (
                'permission_id' => 755,
                'role_id' => 35,
            ),
            120 => 
            array (
                'permission_id' => 755,
                'role_id' => 37,
            ),
            121 => 
            array (
                'permission_id' => 755,
                'role_id' => 38,
            ),
            122 => 
            array (
                'permission_id' => 755,
                'role_id' => 42,
            ),
            123 => 
            array (
                'permission_id' => 755,
                'role_id' => 43,
            ),
            124 => 
            array (
                'permission_id' => 755,
                'role_id' => 47,
            ),
            125 => 
            array (
                'permission_id' => 755,
                'role_id' => 48,
            ),
            126 => 
            array (
                'permission_id' => 756,
                'role_id' => 37,
            ),
            127 => 
            array (
                'permission_id' => 756,
                'role_id' => 38,
            ),
            128 => 
            array (
                'permission_id' => 756,
                'role_id' => 42,
            ),
            129 => 
            array (
                'permission_id' => 756,
                'role_id' => 43,
            ),
            130 => 
            array (
                'permission_id' => 756,
                'role_id' => 44,
            ),
            131 => 
            array (
                'permission_id' => 756,
                'role_id' => 47,
            ),
            132 => 
            array (
                'permission_id' => 756,
                'role_id' => 48,
            ),
            133 => 
            array (
                'permission_id' => 757,
                'role_id' => 35,
            ),
            134 => 
            array (
                'permission_id' => 757,
                'role_id' => 37,
            ),
            135 => 
            array (
                'permission_id' => 757,
                'role_id' => 38,
            ),
            136 => 
            array (
                'permission_id' => 757,
                'role_id' => 42,
            ),
            137 => 
            array (
                'permission_id' => 757,
                'role_id' => 43,
            ),
            138 => 
            array (
                'permission_id' => 757,
                'role_id' => 44,
            ),
            139 => 
            array (
                'permission_id' => 757,
                'role_id' => 47,
            ),
            140 => 
            array (
                'permission_id' => 757,
                'role_id' => 48,
            ),
            141 => 
            array (
                'permission_id' => 758,
                'role_id' => 35,
            ),
            142 => 
            array (
                'permission_id' => 758,
                'role_id' => 37,
            ),
            143 => 
            array (
                'permission_id' => 758,
                'role_id' => 38,
            ),
            144 => 
            array (
                'permission_id' => 758,
                'role_id' => 42,
            ),
            145 => 
            array (
                'permission_id' => 758,
                'role_id' => 43,
            ),
            146 => 
            array (
                'permission_id' => 758,
                'role_id' => 44,
            ),
            147 => 
            array (
                'permission_id' => 758,
                'role_id' => 47,
            ),
            148 => 
            array (
                'permission_id' => 758,
                'role_id' => 48,
            ),
            149 => 
            array (
                'permission_id' => 759,
                'role_id' => 37,
            ),
            150 => 
            array (
                'permission_id' => 759,
                'role_id' => 38,
            ),
            151 => 
            array (
                'permission_id' => 759,
                'role_id' => 42,
            ),
            152 => 
            array (
                'permission_id' => 759,
                'role_id' => 43,
            ),
            153 => 
            array (
                'permission_id' => 759,
                'role_id' => 44,
            ),
            154 => 
            array (
                'permission_id' => 759,
                'role_id' => 47,
            ),
            155 => 
            array (
                'permission_id' => 759,
                'role_id' => 48,
            ),
            156 => 
            array (
                'permission_id' => 760,
                'role_id' => 35,
            ),
            157 => 
            array (
                'permission_id' => 760,
                'role_id' => 37,
            ),
            158 => 
            array (
                'permission_id' => 760,
                'role_id' => 38,
            ),
            159 => 
            array (
                'permission_id' => 760,
                'role_id' => 42,
            ),
            160 => 
            array (
                'permission_id' => 760,
                'role_id' => 43,
            ),
            161 => 
            array (
                'permission_id' => 760,
                'role_id' => 44,
            ),
            162 => 
            array (
                'permission_id' => 760,
                'role_id' => 47,
            ),
            163 => 
            array (
                'permission_id' => 760,
                'role_id' => 48,
            ),
            164 => 
            array (
                'permission_id' => 761,
                'role_id' => 35,
            ),
            165 => 
            array (
                'permission_id' => 761,
                'role_id' => 37,
            ),
            166 => 
            array (
                'permission_id' => 761,
                'role_id' => 38,
            ),
            167 => 
            array (
                'permission_id' => 761,
                'role_id' => 39,
            ),
            168 => 
            array (
                'permission_id' => 761,
                'role_id' => 42,
            ),
            169 => 
            array (
                'permission_id' => 761,
                'role_id' => 43,
            ),
            170 => 
            array (
                'permission_id' => 761,
                'role_id' => 44,
            ),
            171 => 
            array (
                'permission_id' => 761,
                'role_id' => 47,
            ),
            172 => 
            array (
                'permission_id' => 761,
                'role_id' => 48,
            ),
            173 => 
            array (
                'permission_id' => 762,
                'role_id' => 37,
            ),
            174 => 
            array (
                'permission_id' => 762,
                'role_id' => 38,
            ),
            175 => 
            array (
                'permission_id' => 762,
                'role_id' => 45,
            ),
            176 => 
            array (
                'permission_id' => 762,
                'role_id' => 46,
            ),
            177 => 
            array (
                'permission_id' => 762,
                'role_id' => 47,
            ),
            178 => 
            array (
                'permission_id' => 763,
                'role_id' => 37,
            ),
            179 => 
            array (
                'permission_id' => 763,
                'role_id' => 38,
            ),
            180 => 
            array (
                'permission_id' => 763,
                'role_id' => 39,
            ),
            181 => 
            array (
                'permission_id' => 763,
                'role_id' => 40,
            ),
            182 => 
            array (
                'permission_id' => 763,
                'role_id' => 41,
            ),
            183 => 
            array (
                'permission_id' => 763,
                'role_id' => 43,
            ),
            184 => 
            array (
                'permission_id' => 763,
                'role_id' => 47,
            ),
            185 => 
            array (
                'permission_id' => 764,
                'role_id' => 44,
            ),
            186 => 
            array (
                'permission_id' => 765,
                'role_id' => 43,
            ),
            187 => 
            array (
                'permission_id' => 765,
                'role_id' => 44,
            ),
            188 => 
            array (
                'permission_id' => 766,
                'role_id' => 38,
            ),
            189 => 
            array (
                'permission_id' => 766,
                'role_id' => 43,
            ),
            190 => 
            array (
                'permission_id' => 767,
                'role_id' => 43,
            ),
            191 => 
            array (
                'permission_id' => 768,
                'role_id' => 43,
            ),
            192 => 
            array (
                'permission_id' => 769,
                'role_id' => 43,
            ),
            193 => 
            array (
                'permission_id' => 770,
                'role_id' => 10,
            ),
            194 => 
            array (
                'permission_id' => 770,
                'role_id' => 38,
            ),
            195 => 
            array (
                'permission_id' => 775,
                'role_id' => 38,
            ),
            196 => 
            array (
                'permission_id' => 777,
                'role_id' => 41,
            ),
            197 => 
            array (
                'permission_id' => 778,
                'role_id' => 41,
            ),
            198 => 
            array (
                'permission_id' => 780,
                'role_id' => 42,
            ),
        ));
        
        
    }
}
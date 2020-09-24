<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogicTest extends Controller
{
    public function Result(Request $request)
    {
        $grid = $request->values;
        $rows = [];
        $cols = [];
        for ($i=0; $i < count($grid); $i++) {
            array_push($rows, 0);
        }
        for ($i=0; $i < count($grid[0]); $i++) {
            array_push($cols, 0);
        }
        for ($i=0; $i < count($grid); $i++) {
            for ($j=0; $j < count($grid[0]); $j++) {
                $rows[$i] = max($rows[$i], $grid[$i][$j]);
                $cols[$j] = max($cols[$j], $grid[$i][$j]);
            }
        }
        $increaseBy = 0;
        for ($i=0; $i < count($grid); $i++) {
            for ($j=0; $j < count($grid[0]); $j++) {
                $increaseBy += min($rows[$i], $cols[$j]) - $grid[$i][$j];
            }
        }
        return response()->json($increaseBy);
    }
}

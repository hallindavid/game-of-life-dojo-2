<?php

namespace App;

class Grid
{
    /** @var array */
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $this->fillDeadNeighbours($data['data']['grid']);
    }

    public function process(): array
    {
        $newData = [];
        foreach ($this->data as $index => $cell) {
            $neighbours = $this->getAliveNeighbours($cell);

            if (($cell['state'] == 'alive') && ($neighbours < 2 || $neighbours > 3)) {
                // 1 - Any live cell with fewer than two live neighbours dies, as if by underpopulation.
                // 3 - Any live cell with more than three live neighbours dies, as if by overpopulation.
                $cell['state'] = 'dead';
            } elseif (($cell['state'] == 'dead') && ($neighbours == 3)) {
                // 4 - Any dead cell with exactly three live neighbours becomes a live cell, as if by reproduction.
                $cell['state'] = 'alive';
            }

            //Rule is the default scenario as state doesn't change

            //2 - Any live cell with two or three live neighbours lives on to the next generation.

            $newData[$index] = $cell;
        }

        return $newData;
    }

    private function fillDeadNeighbours(array $data): array
    {
        foreach ($data as $cell) {
            $row = intval($cell['row']);
            $col = intval($cell['col']);

            for ($x = $col - 1; $x <= $col + 1; $x++) {
                for ($y = $row - 1; $y <= $row + 1; $y++) {
                    $index = sprintf("%d.%d", $x, $y);
                    if (!isset($data[$index])) {
                        $data[$index] = ['row' => $y, 'col' => $x, 'state' => 'dead'];
                    }
                }
            }
        }
        return $data;
    }

    private function getAliveNeighbours(array $cell): int
    {
        $count = 0;

        $row = intval($cell['row']);
        $col = intval($cell['col']);

        for ($x = $col - 1; $x <= $col + 1; $x++) {
            for ($y = $row - 1; $y <= $row + 1; $y++) {
                $index = sprintf("%d.%d", $x, $y);

                if ($x != $col
                    && $y != $row
                    && ($this->data[$index]['state'] ?? 'dead') == 'alive'
                ) {
                    $count++;
                }
            }
        }
        return $count;
    }
}
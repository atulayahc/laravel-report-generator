<html>
<head>
    </head>
<body>
    <div class="wrapper">
        <div class="content">
            <table width="100%" class="table">
                <?php
                $ctr = 1;
                $no = 1;
                $total = [];
                $grandTotalSkip = 1;
                $currentGroupByData = [];
                $isOnSameGroup = true;

                foreach ($showTotalColumns as $column => $type) {
                    $total[$column] = 0;
                }

                if ($showTotalColumns != []) {
                    foreach ($columns as $colName => $colData) {
                        if (!array_key_exists($colName, $showTotalColumns)) {
                            $grandTotalSkip++;
                        } else {
                            break;
                        }
                    }
                }

                $grandTotalSkip = !$showNumColumn ? $grandTotalSkip - 1 : $grandTotalSkip;
                ?>

                <?php
                $chunkSize = 1000; // Adjust chunk size as needed
                $chunks = $query->when($limit, function ($qry) use ($limit) {
                    $qry->take($limit);
                })->chunk($chunkSize);

                foreach ($chunks as $chunk) {
                    foreach ($chunk as $result) {
                        // Calculate group by data and check if group changed
                        // Similar logic as before...

                        // If group changed, show grand total
                        if ($isOnSameGroup === false) {
                            echo '<tr class="bg-black f-white">';
                            if ($showNumColumn || $grandTotalSkip > 1) {
                                echo '<td colspan="' . $grandTotalSkip . '"><b>' . __('laravel-report-generator::messages.grand_total') . '</b></td>';
                            }
                            $dataFound = false;
                            foreach ($columns as $colName => $colData) {
                                if (array_key_exists($colName, $showTotalColumns)) {
                                    if ($showTotalColumns[$colName] == 'point') {
                                        echo '<td class="right"><b>' . number_format($total[$colName], 2, '.', ',') . '</b></td>';
                                    } else {
                                        echo '<td class="right"><b>' . strtoupper($showTotalColumns[$colName]) . ' ' . number_format($total[$colName], 2, '.', ',') . '</b></td>';
                                    }
                                    $dataFound = true;
                                } else {
                                    if ($dataFound) {
                                        echo '<td></td>';
                                    }
                                }
                            }
                            echo '</tr>';

                            // Reset No & Grand Total
                            $no = 1;
                            foreach ($showTotalColumns as $showTotalColumn => $type) {
                                $total[$showTotalColumn] = 0;
                            }
                            $isOnSameGroup = true;
                        }

                        // Data row
                        echo '<tr align="center" class="' . (($no % 2) == 0 ? 'even' : 'odd') . '">';
                        // ... rest of your logic for each data row ...
                        echo '</tr>';

                        $ctr++;
                        $no++;

                        // Update totals based on columns
                        if (array_key_exists($colName, $showTotalColumns)) {
                            $total[$colName] += $generatedColData;
                        }
                    }
                }
                ?>

                <?php if ($showTotalColumns != [] && $ctr > 1) {
                    echo '<tr class="bg-black f-white">';
                    // ... same logic as before to display grand total ...
                    echo '</tr>';
                } ?>
            </table>
        </div>
    </div>

    </body>
</html>

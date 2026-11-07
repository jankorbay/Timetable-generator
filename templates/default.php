<?php 

    function printEvent(DateTime $start, DateTime $end, string $summary, ?array $description = null, string $classes = '', bool $program = false, bool $required = false) {    
        //according to time, calculate height of box. 2rem = 30min
        $height = (($end->getTimestamp() - $start->getTimestamp()) / 900) * 0.75;
        $time = $start->format('H:i') .' - '. $end->format('H:i');
        $food = containsWholeWord($summary, $GLOBALS['food']);
        echo '
            <div class="box '. ($program ? 'gray_box' : '') . ($food ? 'black_box' : '') . ' min-h-['. $height .'rem] ' . $classes .'">
                <div class="inner_box">        
                    <p>'. $time .'</p>
                    <div>
                        <p class="font-bold">'. $summary .'</p>';
                        if (!is_null($description)) {
                            echo '<p style="line-height: 17px">';
                            foreach ($description as $line) {
                                echo $line . '<br>';
                            }
                        }
            echo '</div>
                </div>';
                if ($required) {
                    echo '
                    <div class="flex justify-end items-center pr-3">
                        '. iconHtml($GLOBALS['requiredIcon'], $GLOBALS['iconColor'], '!h-11 !w-11') .'
                    </div>';
                }
            echo '</div>';
    }

    // One row of the timetable: a single event, or events running at the same time side by side.
    function printGroup(array $group) {
        if (count($group) > 1) {
            /* number of events in one group */
            /* two events at the same time */
            echo '<div class="grid grid-cols-2 gap-1">';
                //check if any of the arrays in the $group array is an array of arrays
                $isArrayOfArrays = false;
                foreach ($group as $event) {
                    // Check if $event is an array and all its elements are also arrays
                    if (is_array($event) && count(array_filter($event, 'is_array')) === count($event) && count($event) > 0) {
                        $isArrayOfArrays = true;
                        break;
                    }
                }
                foreach ($group as $event) {
                     // Check if $event is an array and all its elements are also arrays
                    if (is_array($event) && count(array_filter($event, 'is_array')) === count($event) && count($event) > 0) {
                        echo '<div class="flex flex-col gap-1">';
                        foreach ($event as $sub_event) { // Use a different variable name here
                            printEvent($sub_event['DTSTART'], $sub_event['DTEND'], $sub_event['SUMMARY'], $sub_event['DESCRIPTION'] ?? null, '', ($sub_event['PROGRAM'] ?? false), ($sub_event['REQUIRED'] ?? false));
                        }
                        echo '</div>';
                    } else if (is_array($event)) { // Ensure $event is an array before accessing keys
                        printEvent($event['DTSTART'], $event['DTEND'], $event['SUMMARY'], $event['DESCRIPTION'] ?? null, !$isArrayOfArrays ? 'h-fit' : '', ($event['PROGRAM'] ?? false), ($event['REQUIRED'] ?? false));
                    }
                }
            echo '</div>';
            return;
        }
         if (isset($group[0]) && is_array($group[0])) { // Check if the first element exists and is an array
            printEvent($group[0]['DTSTART'], $group[0]['DTEND'], $group[0]['SUMMARY'], $group[0]['DESCRIPTION'] ?? null, '', ($group[0]['PROGRAM'] ?? false), ($group[0]['REQUIRED'] ?? false));
         }
    }

?>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title !== '' ? htmlspecialchars($title) . ' – ' : '' ?>Harmonogram</title>
    <link rel="icon" href="images/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css?v=<?= filemtime(__DIR__ . '/../styles.css') ?>">
</head>
<body class="timetable <?= htmlspecialchars($theme) ?>" style="<?= $style ?>">
    <?php foreach ($days as $day => $events) { ?>
        <?php include __DIR__ . '/header.php'; ?>
        <h1 class="tt-heading text-center font-['skautbold'] text-3xl mb-5"><?= $week_days[(new DateTime($day))->format('w')] . ' ' . (new DateTime($day))->format('d. m.') ?></h1>
        <div id="boxes" class="flex gap-1 flex-col font-['themix']">
        
        <?php
        $start = 0;
        if ($legend) { // legend beside the first two rows, same place as in the VK Ursus template
            echo '<div class="grid grid-cols-[1fr_30%] gap-1 avoid-break"><div class="flex flex-col gap-1">';
            for (; $start < min(2, count($events)); $start++) {
                printGroup($events[$start]);
            }
            echo '</div><div class="legend-box">'
                . iconHtml($requiredIcon, $iconColor, 'h-10 w-10')
                . '<p class="tt-text">' . htmlspecialchars($legendText) . '</p></div></div>';
        }
        for ($i = $start; $i < count($events); $i++) {
            printGroup($events[$i]);
        }
        //if it's not last iteration, add page break
        if($day != array_key_last($days)) {
            echo '<div class="pagebreak"></div>';
        }
    }
    ?>    
    <div class="footer">
        Vygenerováno na harmonogram.skauting.cz
    </div>
</body>
<?php if (empty($preview)): ?>
<script>
    window.onload = function() { window.print(); }
</script>
<?php endif; ?>
</html>
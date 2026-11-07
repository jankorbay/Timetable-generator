<?php
session_start();

// Create sample calendar data with events across two days
function createSampleEvents() {
    $today = new DateTime();
    $tomorrow = (new DateTime())->add(new DateInterval('P1D'));
    
    // Format dates for display
    $todayFormatted = $today->format('Y-m-d');
    $tomorrowFormatted = $tomorrow->format('Y-m-d');
    
    // Sample events; ACTIVITY/TOPIC/ORGANIZATIONAL color the Pecka template
    $events = [
        // Day 1 events
        [
            'DTSTART' => (clone $today)->setTime(8, 0),
            'DTEND' => (clone $today)->setTime(8, 30),
            'SUMMARY' => 'Snídaně',
            'DESCRIPTION' => null
        ],
        [
            'DTSTART' => (clone $today)->setTime(9, 0),
            'DTEND' => (clone $today)->setTime(10, 30),
            'SUMMARY' => 'Zahájení dne',
            'ORGANIZATIONAL' => true,
            'DESCRIPTION' => ['přivítání', 'informace o programu'],
            'PROGRAM' => true
        ],
        [
            'DTSTART' => (clone $today)->setTime(10, 45),
            'DTEND' => (clone $today)->setTime(12, 15),
            'SUMMARY' => 'Dopolední program - Orientace v přírodě',
            'ACTIVITY' => true,
            'DESCRIPTION' => ['práce s mapou a buzolou', 'orientační běh'],
            'PROGRAM' => true,
            'REQUIRED' => true
        ],
        [
            'DTSTART' => (clone $today)->setTime(12, 30),
            'DTEND' => (clone $today)->setTime(13, 30),
            'SUMMARY' => 'Oběd',
            'DESCRIPTION' => null
        ],
        [
            'DTSTART' => (clone $today)->setTime(14, 0),
            'DTEND' => (clone $today)->setTime(16, 0),
            'SUMMARY' => 'Odpolední program - Tábornické dovednosti',
            'TOPIC' => true,
            'DESCRIPTION' => ['stavba přístřešku', 'rozdělávání ohně'],
            'PROGRAM' => true
        ],
        [
            'DTSTART' => (clone $today)->setTime(16, 15),
            'DTEND' => (clone $today)->setTime(17, 0),
            'SUMMARY' => 'Svačina',
            'DESCRIPTION' => null
        ],
        [
            'DTSTART' => (clone $today)->setTime(17, 30),
            'DTEND' => (clone $today)->setTime(19, 0),
            'SUMMARY' => 'Týmové hry',
            'ACTIVITY' => true,
            'DESCRIPTION' => ['strategické hry', 'budování týmu'],
            'PROGRAM' => true
        ],
        [
            'DTSTART' => (clone $today)->setTime(19, 0),
            'DTEND' => (clone $today)->setTime(20, 0),
            'SUMMARY' => 'Večeře',
            'DESCRIPTION' => null
        ],
        [
            'DTSTART' => (clone $today)->setTime(20, 30),
            'DTEND' => (clone $today)->setTime(22, 0),
            'SUMMARY' => 'Večerní program - Táborák',
            'TOPIC' => true,
            'DESCRIPTION' => ['zpěv', 'povídání', 'hry'],
            'PROGRAM' => true
        ],
        
        // Day 2 events
        [
            'DTSTART' => (clone $tomorrow)->setTime(7, 30),
            'DTEND' => (clone $tomorrow)->setTime(8, 0),
            'SUMMARY' => 'Budíček a ranní hygiena',
            'DESCRIPTION' => null
        ],
        [
            'DTSTART' => (clone $tomorrow)->setTime(8, 0),
            'DTEND' => (clone $tomorrow)->setTime(8, 45),
            'SUMMARY' => 'Snídaně',
            'DESCRIPTION' => null
        ],
        [
            'DTSTART' => (clone $tomorrow)->setTime(9, 0),
            'DTEND' => (clone $tomorrow)->setTime(9, 30),
            'SUMMARY' => 'Ranní nástup',
            'ORGANIZATIONAL' => true,
            'DESCRIPTION' => ['informace o programu dne'],
            'PROGRAM' => true
        ],
        [
            'DTSTART' => (clone $tomorrow)->setTime(9, 45),
            'DTEND' => (clone $tomorrow)->setTime(12, 0),
            'SUMMARY' => 'Celodenní výprava - část 1',
            'ACTIVITY' => true,
            'DESCRIPTION' => ['pěší výlet do okolí', 'poznávání přírody'],
            'PROGRAM' => true,
            'REQUIRED' => true
        ],
        [
            'DTSTART' => (clone $tomorrow)->setTime(12, 0),
            'DTEND' => (clone $tomorrow)->setTime(13, 0),
            'SUMMARY' => 'Oběd v terénu',
            'DESCRIPTION' => null
        ],
        [
            'DTSTART' => (clone $tomorrow)->setTime(13, 30),
            'DTEND' => (clone $tomorrow)->setTime(16, 30),
            'SUMMARY' => 'Celodenní výprava - část 2',
            'TOPIC' => true,
            'DESCRIPTION' => ['plnění úkolů', 'týmová spolupráce'],
            'PROGRAM' => true
        ],
        [
            'DTSTART' => (clone $tomorrow)->setTime(17, 0),
            'DTEND' => (clone $tomorrow)->setTime(18, 0),
            'SUMMARY' => 'Osobní volno',
            'DESCRIPTION' => null
        ],
        [
            'DTSTART' => (clone $tomorrow)->setTime(18, 0),
            'DTEND' => (clone $tomorrow)->setTime(19, 0),
            'SUMMARY' => 'Večeře',
            'DESCRIPTION' => null
        ],
        [
            'DTSTART' => (clone $tomorrow)->setTime(19, 30),
            'DTEND' => (clone $tomorrow)->setTime(21, 0),
            'SUMMARY' => 'Večerní program - Reflexe dne',
            'TOPIC' => true,
            'DESCRIPTION' => ['sdílení zážitků', 'hodnocení aktivit'],
            'PROGRAM' => true
        ]
    ];
    
    return $events;
}

// Group events by day
function groupEventsByDay($events) {
    $days = [];
    foreach($events as $event) {
        $day = $event['DTSTART']->format('Y-m-d');
        $days[$day][] = $event;
    }
    
    // Sort events by time within each day
    foreach($days as $key => $day) {
        usort($day, function($a, $b) {
            return $a['DTSTART'] <=> $b['DTSTART'];
        });
        $days[$key] = $day;
    }
    
    ksort($days);
    
    // Group events happening at the same time (simplified version)
    $groupedDays = [];
    foreach ($days as $day => $events) {
        $grouped = [];
        foreach ($events as $event) {
            $grouped[] = [$event];
        }
        $groupedDays[$day] = $grouped;
    }
    
    return $groupedDays;
}

// Generate sample data
$sampleEvents = createSampleEvents();
$days = groupEventsByDay($sampleEvents);

// Template and customizations from the query string (same keys as the form), falling back to Ursus;
// ?preview=1 skips the print dialog (used by the preview window)
$settings = $_GET;
$fallback = 'ursus';
$preview = isset($_GET['preview']);
require __DIR__ . '/templates/render.php';
?>

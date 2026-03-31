<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <base href="<?php echo $domena; ?>/">
    <title>Online event planner</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/my_css.css">
    
    <script>
        window.myCalendarEvents = <?php echo isset($events_json) ? $events_json : '[]'; ?>; // prohlizece hazeli cyhbu, proto kdyztak nastavujeme prazdny pole
        window.domena = "<?php echo $domena; ?>";
    </script>
</head>
<body>
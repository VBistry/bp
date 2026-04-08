<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <base href="<?php echo $domena; ?>/">
    <title>Online event planner</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
     <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css"> -->

    <link rel="stylesheet" href="css/my_css.css">
    
    <script>
        var myCalendarEvents = <?php echo isset($events_json) ? $events_json : '[]'; ?>;
        var domena = "<?php echo $domena; ?>";
    </script>
</head>
<body>
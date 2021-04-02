<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Search for (global) cities and sightsee information on the web.">
    <meta name="keywords" content="search engine, CITYsearch, cities, countries, sightseeing, places, inspiration, travel">
    <meta name="author" content="Kevin de Borst">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="assets/css/fontawesome.css">
    <link rel="stylesheet" type="text/css" href="assets/css/solid.css">
    <link rel="stylesheet" type="text/css" href="assets/css/citysearch_default.css">
    
    <title>CITYsearch || Search For Sightseeing(s) At Home</title>
</head>
<body> 
    <main class="wrapper index">
        <div class="logo-container">
            <a href="index.php"><i class="fas fa-search-location"></i> CITYsearch!</a>
        </div>
        <div class="search-container">
            <form action="citysearch.php" method="GET">
                <input type="text" class="search-box" name="search">
                <input type="submit" class="search-button" value="Search">
            </form>
        </div>
    </main>
</body>
</html>
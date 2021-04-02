<?php

    //Get related files
    include 'config/config.php';
    include 'includes/classes/WeblinkResultsProvider.php';

    //Get search(term)
    $search = isset($_GET['search']) ? $_GET['search'] : exit('Please enter a CITY to search..');
    
    //Get search category ['type']
    $type = isset($_GET['type']) ? $_GET['type'] : 'websites';

    //Get page number or set default to 1
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    
?>
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
    <link rel="stylesheet" type="text/css" href="assets/css/brands.css">
    <link rel="stylesheet" type="text/css" href="assets/css/citysearch_default.css">

    <!-- Javascript -->
    <script src="assets/js/jquery-3.4.1.min.js"></script>
    <script src="assets/js/citysearch_default.js"></script>

    <title>Welcome To CITYsearch</title>

</head>
<body>
    <div class="wrapper">
        <header class="header">
            <header class="header-content">
                <div class="logo-container">
                    <a href="index.php"><i class="fas fa-search-location"></i> CITYsearch!</a>
                </div>
                <div class="search-container">
                    <form action="citysearch.php" method="GET">
                        <div class="search-bar">
                            <input type="text" class="search-box" name="search" value="<?php echo $search ?>">
                            <button class="search-button"><i class="fas fa-search" style="font-size:22px;"></i></button>
                        </div>
                    </form>
                </div>
            </header>
            <header class="tabs">
                <ul class="tablist">
                    <li class="<?php echo $type == 'websites' ? 'active' : ''; ?>">
                        <a class="<?php echo $type == 'websites' ? 'active' : ''; ?>" href='<?php echo "citysearch.php?search=$search&type=websites"; ?>'>
                        <i class="fas fa-globe-europe" id="<?php echo $type == 'websites' ? 'active' : ''; ?>"></i> Websites
                        </a>
                    </li>
                    <li class="<?php echo $type == 'citysights' ? 'active' : ''; ?>">
                        <a class="<?php echo $type == 'citysights' ? 'active' : ''; ?>" href='<?php echo "citysearch.php?search=$search&type=citysights"; ?>'>
                        <i class="fas fa-map-marked-alt" id="<?php echo $type == 'citysights' ? 'active' : ''; ?>"></i> CITYsights
                        </a>
                    </li>
                    <li class="<?php echo $type == 'images' ? 'active' : ''; ?>">
                        <a class="<?php echo $type == 'images' ? 'active' : ''; ?>" href='<?php echo "citysearch.php?search=$search&type=images"; ?>'>
                        <i class="fas fa-search-location" id="<?php echo $type == 'images' ? 'active' : ''; ?>"></i> Images
                        </a>
                    </li>
                </ul>
            </header>
        </header>
        <main class="results-section">
            <?php
                $weblink_provider = new WeblinkResultsProvider($con);
                $page_limit = 20;
                $number_results = $weblink_provider->getNumResults($search);
                
                echo "<p class='results-count'>$number_results results found</p>";

                echo $weblink_provider->getHtmlResults($page, $page_limit, $search);
            ?>
        </main>
        <footer class="pagination-container">
            <div class="page-buttons">    
                <div class="page-markers">
                    <a>CITY&nbsp;</a>
                </div>
                <?php
                    $current_page = 1;
                    $pages_remaining = 10;

                    while($pages_remaining != 0) {
                        
                        if($current_page == $page) {
                            echo "<div class='page-markers'>
                                    <span class='page-number'>
                                        <i class='fas fa-map-marker-alt'></i>
                                        <p>$current_page</p>
                                    </span>
                              </div>";
                        } else {
                            echo "<div class='page-markers'>
                                    <a href='citysearch.php?search=$search&type=$type&page=$current_page'>
                                        <span class='page-number'>
                                            <i class='fas fa-map-marker-alt'></i>
                                            <p>$current_page</p>
                                        </span>
                                    </a>
                              </div>";
                        }

                        $current_page++;
                        $pages_remaining--;
                    }
                ?>
                <div class="page-markers">
                    <a>&nbsp;search</a>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
<?php

//Get related files
include 'config/config.php';
include 'includes/classes/DomDocumentParser.php';

$already_crawled = array();
$crawling = array();
$existing_images = array(); //$alreadyFoundImages by Reece

function createLink($src, $url) {
    //Initialising variables
    $scheme = parse_url($url)["scheme"];
    $host = parse_url($url)["host"];

    //Relative Link Convertors & Error Handlers:
    if(substr($src, 0, 2) == "//") {
        $src = $scheme . ":" . $src;
    }
    elseif(substr($src, 0, 1) == "/") {
        $src = $scheme . "://" . $host . $src;
    }
    elseif(substr($src, 0, 2) == "./") {
        $src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1);
    }
    elseif(substr($src, 0, 3) == "../") {
        $src = $scheme . "://" . $host . "/" . $src;
    }
    elseif(substr($src, 0, 5) != "https" && substr($src, 0, 4) != "http") {
        $src = $scheme . "://" . $host . "/" . $src;
    }

    return $src;
}

function insertLink($url, $title, $description, $keywords) {
    global $con;

    $stmt = $con->prepare("
        INSERT INTO websites (url, title, description, keywords)
        VALUES (:url, :title, :description, :keywords)");

    $stmt->bindParam(":url", $url);
    $stmt->bindParam(":title", $title);
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":keywords", $keywords);

    return $stmt->execute();
}

function insertImage($url, $src, $alt, $title) {
    global $con;

    $stmt = $con->prepare("
        INSERT INTO images (website_url, image_url, alt, title)
        VALUES (:website_url, :image_url, :alt, :title)");

    $stmt->bindParam(":website_url", $url);
    $stmt->bindParam(":image_url", $src);
    $stmt->bindParam(":alt", $alt);
    $stmt->bindParam(":title", $title);

    return $stmt->execute();
}

function linkExists($url) {
    global $con;

    $stmt = $con->prepare("
        SELECT * FROM websites WHERE url=:url");

    $stmt->bindParam(":url", $url);
    $stmt->execute();

    return $stmt->rowCount() != 0;
}

function getDetails($url) {
    global $existing_images;
    
    //Instantiating object
    $parser = new DomDocumentParser($url);

    $title_array = $parser->getTitleTags();

    if(sizeof($title_array) == 0 || $title_array->item(0) == NULL) {
        return;
    }

    $title = $title_array->item(0)->nodeValue;
    $title = str_replace("\n", "", $title);

    if($title == "") {
        return;
    }

    $description = "";
    $keywords = "";

    $meta_array = $parser->getMetaTags();

    foreach($meta_array as $meta) {
        
        if($meta->getAttribute("name") == "description") {
            $description = $meta->getAttribute("content");
        }

        if($meta->getAttribute("name") == "keywords") {
            $keywords = $meta->getAttribute("content");
        }
    }

    $description = str_replace("\n", "", $description);
    $keywords = str_replace("\n", "", $keywords);

    if(linkExists($url)) {
        echo "$url already exists<br>";
    } 
    elseif(insertLink($url, $title, $description, $keywords)) {
        echo "SUCCESS: $url<br>";
    }
    else {
        echo "ERROR: Failed to insert $url<br>";
    }
    
    $image_array = $parser->getImages();

    foreach($image_array as $image) {
        $src = $image->getAttribute("src");
        $alt = $image->getAttribute("alt");
        $title = $image->getAttribute("title");

        if(!$title && !$alt) {
            continue;
        }

        $src = createLink($src, $url);

        if(!in_array($src, $existing_images)) {
            $existing_images[] = $src;

            insertImage($url, $src, $alt, $title);
        }
    }
}

function followLinks($url) {
    
    global $already_crawled;
    global $crawling;
    
    //Instantiating object
    $parser = new DomDocumentParser($url);

    //Retrieve an overview of the linklist
    $linkList = $parser->getLinks();

    //Convert and exclude 'relative' items from absolute hyperlink generation
    foreach($linkList as $link) {
        //Get all link attributes from overviewLinks
        $href = $link->getAttribute("href");
        
        //Exclude href-attributes from hyperlink generation process
        if(strpos($href, "#") !== false) {
            continue;
        }
        elseif(substr($href, 0, 11)  == "javascript:") {
            continue;
        }
        
        //Generate attributes into weblinks
        $href = createLink($href, $url);
        
        if(!in_array($href, $already_crawled)) {
            $already_crawled[] = $href;
            $crawling[] = $href;

            getDetails($href);
        } 
    }

    array_shift($crawling);

    foreach($crawling as $website) {
        followLinks($website);
    }

}

$start_url = "https://www.worlddata.info/capital-cities.php";
followLinks($start_url);
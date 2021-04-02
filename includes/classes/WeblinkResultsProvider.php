<?php

class WeblinkResultsProvider {

    private $con;

    public function __construct($con) {
        $this->con = $con;
    }

    public function getNumResults($search) {
        $stmt = $this->con->prepare("SELECT COUNT(*) as total FROM websites 
                                    WHERE title LIKE :search 
                                    OR url LIKE :search 
                                    OR keywords LIKE :search 
                                    OR description LIKE :search");

        $search_term = "%" . $search . "%";    
        $stmt->bindParam(":search", $search_term);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row["total"];

    }

    private function fieldTrim($string, $char_limit) {
        $dots = strlen($string) > $char_limit ? "..." : "";
        return substr($string, 0, $char_limit) . $dots;
    }

    public function getHtmlResults($page, $page_size, $search) {
        $start_limit = ($page - 1) * $page_size;
        
        $stmt = $this->con->prepare("SELECT * FROM websites
                                    WHERE title LIKE :search 
                                    OR url LIKE :search 
                                    OR keywords LIKE :search 
                                    OR description LIKE :search
                                    ORDER BY clicks DESC
                                    LIMIT :start_limit, :page_size");

        $search_term = "%" . $search . "%";    
        $stmt->bindParam(":search", $search_term);
        $stmt->bindParam(":start_limit", $start_limit, PDO::PARAM_INT);
        $stmt->bindParam(":page_size", $page_size, PDO::PARAM_INT);
        $stmt->execute();

        $results_html = "<section class='website-results'>";
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
            $url = $row["url"];
            $title = $row["title"];
            $description = $row["description"];

            $title = $this->fieldTrim($title, 52);
            $description = $this->fieldTrim($description, 150);

            $results_html .= "<section class='result-container'>
                                    <h3 class='title'>
                                        <a class='result' href='$url'>$title</a>
                                    </h3>
                                    <span class='url'>$url</span>
                                    <span class='description'>$description</span>
                              </section>";
        }

        $results_html .= "</section>";

        return $results_html;
    }
}
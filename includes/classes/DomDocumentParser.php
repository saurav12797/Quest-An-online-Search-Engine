<?php

class DomDocumentParser {
    private $document;

    //Constructor
    public function __construct($url) {
        $options = array('http'=>array('method'=>"GET", 'header'=>"User-Agent: CITYsearchBot/0.1\n"));
        $context = stream_context_create($options);

        $this->document = new DomDocument();
        @$this->document->loadHTML(file_get_contents($url, false, $context));
    }

    public function getLinks() {
        return $this->document->getElementsByTagName("a");
    }

    public function getTitleTags() {
        return $this->document->getElementsByTagName("title");
    }

    public function getMetaTags() {
        return $this->document->getElementsByTagName("meta");
    }

    public function getImages() {
        return $this->document->getElementsByTagName("img");
    }
}
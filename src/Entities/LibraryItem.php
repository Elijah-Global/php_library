<?php

namespace LibrarySystem\Entities;

abstract class LibraryItem {
    protected string $title;
    protected string $author;
    protected string $id;
    
    public function __construct(string $title, string $author, string $id) {
        $this->title = $title;
        $this->author = $author;
        $this->id = $id;
    }
    
    abstract public function getDetails(): string;
    
    public function getTitle(): string {
        return $this->title;
    }
    
    public function getAuthor(): string {
        return $this->author;
    }
    
    public function getId(): string {
        return $this->id;
    }
}
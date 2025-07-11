<?php

namespace LibrarySystem\Entities;
use InvalidArgumentException;
use LibrarySystem\Entities\Actions\Borrowing;

class Member {
    private string $memberId;
    private string $name;
    private string $email;
    private array $borrowedBooks;
    
    public function __construct(string $memberId, string $name, string $email) {
        $this->memberId = $memberId;
        $this->name = $name;
        $this->email = $this->validateEmail($email);
        $this->borrowedBooks = [];
    }
    
    private function validateEmail(string $email): string {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email address");
        }
        return $email;
    }
    
    public function getMemberId(): string {
        return $this->memberId;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function getEmail(): string {
        return $this->email;
    }
    
    public function getBorrowedBooks(): array {
        return $this->borrowedBooks;
    }
    
    public function addBorrowedBook(Borrowing $borrowing): void {
        $this->borrowedBooks[] = $borrowing;
    }
    
    public function removeBorrowedBook(Borrowing $borrowing): void {
        $this->borrowedBooks = array_filter($this->borrowedBooks, fn($b) => $b !== $borrowing);
    }
}
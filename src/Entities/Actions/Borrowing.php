<?php

namespace LibrarySystem\Entities\Actions;

use LibrarySystem\Entities\Borrowable;
use LibrarySystem\Entities\Member;

class Borrowing {
    private Borrowable $item;
    private Member $member;
    private \DateTime $borrowDate;
    private ?\DateTime $returnDate;
    private ?\DateTime $dueDate;
    
    public function __construct(Borrowable $item, Member $member) {
        $this->item = $item;
        $this->member = $member;
        $this->borrowDate = new \DateTime();
        $this->dueDate = (new \DateTime())->modify('+14 days');
        $this->returnDate = null;
    }
    
    public function getItem(): Borrowable {
        return $this->item;
    }
    
    public function getMember(): Member {
        return $this->member;
    }
    
    public function getBorrowDate(): \DateTime {
        return $this->borrowDate;
    }
    
    public function getDueDate(): ?\DateTime {
        return $this->dueDate;
    }
    
    public function getReturnDate(): ?\DateTime {
        return $this->returnDate;
    }
    
    public function returnBook(): bool {
        if ($this->item->returnItem()) {
            $this->returnDate = new \DateTime();
            return true;
        }
        return false;
    }
}
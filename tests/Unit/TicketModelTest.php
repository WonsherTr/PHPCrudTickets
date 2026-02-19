<?php

namespace Tests\Unit;

use App\Models\Ticket;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class TicketModelTest extends TestCase
{
    public function test_statuses_constant_has_four_values(): void
    {
        $this->assertCount(4, Ticket::STATUSES);
        $this->assertContains('OPEN', Ticket::STATUSES);
        $this->assertContains('IN_PROGRESS', Ticket::STATUSES);
        $this->assertContains('RESOLVED', Ticket::STATUSES);
        $this->assertContains('CLOSED', Ticket::STATUSES);
    }

    public function test_priorities_constant_has_four_values(): void
    {
        $this->assertCount(4, Ticket::PRIORITIES);
        $this->assertContains('LOW', Ticket::PRIORITIES);
        $this->assertContains('MEDIUM', Ticket::PRIORITIES);
        $this->assertContains('HIGH', Ticket::PRIORITIES);
        $this->assertContains('URGENT', Ticket::PRIORITIES);
    }

    public function test_user_is_admin_helper(): void
    {
        $user = new User();
        $user->role = 'admin';
        $this->assertTrue($user->isAdmin());

        $user->role = 'user';
        $this->assertFalse($user->isAdmin());
    }
}

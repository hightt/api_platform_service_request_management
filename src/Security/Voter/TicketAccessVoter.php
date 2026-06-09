<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Ticket;
use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Ticket>
 */
class TicketAccessVoter extends Voter
{
    public const EDIT = 'TICKET_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::EDIT && $subject instanceof Ticket;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        if ($user->hasRole(UserRole::ADMIN)) {
            return true;
        }

        $technician = $user->getTechnician();
        if (!$technician) {
            return false;
        }

        $assignedTechnician = $subject->getAssignedTechnician();

        return !is_null($assignedTechnician) && $assignedTechnician->getId() === $technician->getId();
    }
}

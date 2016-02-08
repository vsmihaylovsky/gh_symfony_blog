<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 2/8/16
 * Time: 6:17 PM
 */

namespace AppBundle\Security;

use AppBundle\Entity\Comment;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class CommentVoter extends Voter
{
    const EDIT = 'edit';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::EDIT))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Comment) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }

        // you know $subject is a Post object, thanks to supports
        /** @var Comment $comment */
        $comment = $subject;

        switch($attribute) {
            case self::EDIT:
                return $this->canEdit($comment, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Comment $comment, User $user)
    {
        if (($user === $comment->getArticle()->getUser()) && (!in_array('ROLE_ADMIN', $comment->getUser()->getRoles()))) {
            return true;
        }

        if ($user === $comment->getUser()) {
            return true;
        }

        return false;
    }
}
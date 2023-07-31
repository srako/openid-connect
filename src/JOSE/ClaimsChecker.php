<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\JOSE;

use Srako\OpenIDConnect\Exception\AuthorizationException;
use Srako\OpenIDConnect\Param\ClaimsChecks;
use Jose\Component\Checker\ClaimCheckerManager;
use Jose\Component\Checker\InvalidClaimException;
use Jose\Component\Checker\MissingMandatoryClaimException;

final class ClaimsChecker implements ClaimsCheckerInterface
{
    public function check(Claims $claims, ClaimsChecks $checks): void
    {
        try {
            $checker = new ClaimCheckerManager($checks->checkers());
            $checker->check($claims->all(), $checks->mandatoryClaims());
        } catch (MissingMandatoryClaimException $e) {
            throw new AuthorizationException('Missing claims: ' . $e->getMessage(), 0, $e);
        } catch (InvalidClaimException $e) {
            throw new AuthorizationException('Invalid claims: ' . $e->getMessage(), 0, $e);
        }
    }
}

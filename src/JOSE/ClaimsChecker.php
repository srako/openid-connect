<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\JOSE;

use Jose\Component\Checker\ClaimCheckerManager;
use Jose\Component\Checker\InvalidClaimException;
use Jose\Component\Checker\MissingMandatoryClaimException;
use Srako\OpenIDConnect\Exception\AuthorizationException;
use Srako\OpenIDConnect\Param\ClaimsChecks;

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

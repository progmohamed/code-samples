<?php

namespace AdminBundle\ExpressionLanguage;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            new ExpressionFunction('lowercase', function ($str) {
                return sprintf('(is_string(%1$s) ? strtolower(%1$s) : %1$s)', $str);
            }, function ($arguments, $str) {
                if (!is_string($str)) {
                    return $str;
                }
                return strtolower($str);
            }),
            new ExpressionFunction('has_role', function ($role) {
                return sprintf('in_array(%s, $roles)', $role);
            }, function ($arguments, $role) {
                return in_array('ROLE_SUPER_ADMIN', $arguments['roles']) || in_array($role, $arguments['roles']);
            }),
        ];
    }
  
}
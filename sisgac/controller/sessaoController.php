<?php
class sessaoController {

    public function validar($perfis) {
        $erro = 0;
        if (!isset($_SESSION['ativo'])) {
            $erro = 1;
        } else {
            $acesso = false;
            foreach ($perfis as $perfil) {
                if ($perfil == $_SESSION['perfil']) {
                    $acesso = true;
                    break;
                }
            }
            if (!$acesso) {
                $erro = 2;
            }
        }
        return $erro;
    }
}


<?php

namespace App\Twig;

use App\Service\Antispam;

class AntispamExtension extends \Twig_Extension
{
  /**
   * @var antispam
   */
  private $antispam;

  public function __construct(Antispam $antispam)
  {
    $this->antispam = $antispam;
  }

  public function checkIfArgumentIsSpam($text)
  {
    return $this->antispam->isSpam($text);
  }

  // Twig va exécuter cette méthode pour savoir quelle(s) fonction(s) ajoute notre service
  public function getFunctions()
  {
    return array(
      new \Twig_SimpleFunction('checkIfSpam', array($this, 'checkIfArgumentIsSpam')),
    );
  }

  // La méthode getName() identifie votre extension Twig, elle est obligatoire
  public function getName()
  {
    return 'Antispam';
  }
}

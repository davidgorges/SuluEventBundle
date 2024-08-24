<?php

declare(strict_types=1);

namespace Manuxi\SuluEventBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use JMS\Serializer\Annotation as Serializer;
use Manuxi\SuluEventBundle\Entity\Interfaces\ExcerptInterface;
use Manuxi\SuluEventBundle\Entity\Interfaces\ExcerptTranslatableInterface;
use Manuxi\SuluEventBundle\Entity\Traits\ExcerptTrait;
use Manuxi\SuluEventBundle\Entity\Traits\ExcerptTranslatableTrait;
use Manuxi\SuluEventBundle\Repository\EventExcerptRepository;

#[ORM\Entity(repositoryClass: EventExcerptRepository::class)]
#[ORM\Table(name: 'app_event_excerpt')]
class EventExcerpt implements ExcerptInterface, ExcerptTranslatableInterface
{
    use ExcerptTrait;
    use ExcerptTranslatableTrait;

    #[Serializer\Exclude]
    #[ORM\OneToOne(inversedBy: 'eventExcerpt', targetEntity: Event::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: "id", nullable: false)]
    private ?Event $event = null;

    #[Serializer\Exclude]
    #[ORM\OneToMany(mappedBy: 'eventExcerpt', targetEntity: EventExcerptTranslation::class, cascade: ['all'], indexBy: 'locale')]
    private Collection $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): self
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @return EventExcerptTranslation[]
     */
    public function getTranslations(): array
    {
        return $this->translations->toArray();
    }

    protected function getTranslation(string $locale): ?EventExcerptTranslation
    {
        if (!$this->translations->containsKey($locale)) {
            return null;
        }

        return $this->translations->get($locale);
    }

    protected function createTranslation(string $locale): EventExcerptTranslation
    {
        $translation = new EventExcerptTranslation($this, $locale);
        $this->translations->set($locale, $translation);

        return $translation;
    }

}

<?php

namespace App\Notifications;

class IftttMessage
{
    /**
     * The title of the message.
     *
     * @var string
     */
    public $title;

    /**
     * The text content of the message.
     *
     * @var string
     */
    public $content;

    /**
     * Set the title of the IFTTT message.
     *
     * @param  string  $title
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set the content of the IFTTT message.
     *
     * @param  string  $content
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;

        return $this;
    }
}

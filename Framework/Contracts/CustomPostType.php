<?php


namespace TheLoop\Contracts;


interface CustomPostType
{
    /**
     * Define the labels for your custom post type here
     * @return mixed
     */
    public function defineLabels();

    /**
     * setup your custom post type here and run register post type hook
     * @return mixed
     */
    public function registerPostType();

    /**
     * run definelabels function, add init action with registerPostType method
     * add any other added functionality needed for post type
     * @return mixed
     */
    public function buildPostType();
}
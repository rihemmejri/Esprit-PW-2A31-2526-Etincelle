<?php

class AIPrediction
{
    private $id;
    private $user_id;
    private $date;
    private $input_data;
    private $prediction;
    private $risk_level;

    public function __construct($user_id, $date, $input_data, $prediction, $risk_level)
    {
        $this->user_id = $user_id;
        $this->date = $date;
        $this->input_data = $input_data;
        $this->prediction = $prediction;
        $this->risk_level = $risk_level;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->user_id; }
    public function getDate() { return $this->date; }
    public function getInputData() { return $this->input_data; }
    public function getPrediction() { return $this->prediction; }
    public function getRiskLevel() { return $this->risk_level; }

    // Setters
    public function setId($id) { $this->id = $id; }
}

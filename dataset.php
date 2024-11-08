<?php
session_start();

class Node {
    public $feature;
    public $threshold;
    public $left;
    public $right;
    public $value;

    function __construct($feature = null, $threshold = null, $left = null, $right = null, $value = null) {
        $this->feature = $feature;
        $this->threshold = $threshold;
        $this->left = $left;
        $this->right = $right;
        $this->value = $value;
    }
}

function calculateGiniIndex($groups, $classes) {
    $totalInstances = array_sum(array_map('count', $groups));
    if ($totalInstances == 0) {
        return 0.0; // Avoid division by zero
    }
    $giniIndex = 0.0;
    foreach ($groups as $group) {
        $size = count($group);
        if ($size == 0) {
            continue;
        }
        $score = 0.0;
        foreach ($classes as $classVal) {
            $p = count(array_filter($group, function($row) use ($classVal) {
                return $row[count($row) - 1] == $classVal;
            })) / $size;
            $score += $p * $p;
        }
        $giniIndex += (1.0 - $score) * ($size / $totalInstances);
    }
    return $giniIndex;
}

function splitDataset($dataset, $feature, $threshold) {
    $left = [];
    $right = [];
    foreach ($dataset as $data) {
        if ($data[$feature] < $threshold) {
            $left[] = $data;
        } else {
            $right[] = $data;
        }
    }
    return [$left, $right];
}

function findBestSplit($dataset) {
    $classValues = array_unique(array_column($dataset, count($dataset[0]) - 1));
    $bestFeature = 0;
    $bestThreshold = 0;
    $bestGini = PHP_INT_MAX;
    for ($feature = 0; $feature < count($dataset[0]) - 1; $feature++) {
        $featureValues = array_unique(array_column($dataset, $feature));
        foreach ($featureValues as $threshold) {
            $groups = splitDataset($dataset, $feature, $threshold);
            $gini = calculateGiniIndex($groups, $classValues);
            if ($gini < $bestGini) {
                $bestFeature = $feature;
                $bestThreshold = $threshold;
                $bestGini = $gini;
            }
        }
    }
    return [$bestFeature, $bestThreshold];
}

function buildDecisionTree($dataset, $maxDepth, $minSize, $depth) {
    if (empty($dataset)) {
        throw new InvalidArgumentException("Dataset is empty");
    }
    $classValues = array_unique(array_column($dataset, count($dataset[0]) - 1));
    // Base cases
    if ($depth >= $maxDepth || count($classValues) == 1 || count($dataset) <= $minSize) {
        $counts = array_count_values(array_column($dataset, count($dataset[0]) - 1));
        $mostCommonClass = array_search(max($counts), $counts);
        return new Node(null, null, null, null, $mostCommonClass);
    }
    list($bestFeature, $bestThreshold) = findBestSplit($dataset);
    list($left, $right) = splitDataset($dataset, $bestFeature, $bestThreshold);
    if (empty($left) || empty($right)) {
        $counts = array_count_values(array_column($dataset, count($dataset[0]) - 1));
        $mostCommonClass = array_search(max($counts), $counts);
        return new Node(null, null, null, null, $mostCommonClass);
    }
    $leftNode = buildDecisionTree($left, $maxDepth, $minSize, $depth + 1);
    $rightNode = buildDecisionTree($right, $maxDepth, $minSize, $depth + 1);
    return new Node($bestFeature, $bestThreshold, $leftNode, $rightNode);
}

function predict($node, $sample) {
    if ($node->value !== null) {
        return $node->value;
    }
    if ($sample[$node->feature] < $node->threshold) {
        return predict($node->left, $sample);
    } else {
        return predict($node->right, $sample);
    }
}

// // Sample data
// $data = array(
//     array(1, 1),
//     array(1, 2),
//     array(1, 3),
//     array(0)
// );

if(empty($data)) {
    throw new InvalidArgumentException("Training data cannot be empty");
}

// Build decision tree
$decisionTree = buildDecisionTree($data, 5, 2, 0);

// Sample test data
if (isset($_POST['submit'])) {
    $sampleData = array($_POST['clearance'], $_POST['cs']); // Student's features (documents, tuition fee, grades, clearance)

    // Predict
    $prediction = predict($decisionTree, $sampleData);

    echo "Prediction: " . ($prediction ? "Eligible" : "Not Eligible") . "\n";
}

?>

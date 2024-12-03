<?php

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
    $classValues = array_unique(array_column($dataset, count($dataset[0]) - 1));
    // Base cases
    if ($depth >= $maxDepth || count($classValues) == 1 || count($dataset) <= $minSize) {
        $counts = array_count_values(array_column($dataset, count($dataset[0]) - 1));
        $mostCommonClass = array_search(max($counts), $counts);
        return new Node(null, null, null, null, $mostCommonClass);
    }
    list($bestFeature, $bestThreshold) = findBestSplit($dataset);
    list($left, $right) = splitDataset($dataset, $bestFeature, $bestThreshold);
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

// Sample data
$data = array(
    // Student's features (documents, tuition fee, grades, clearance, course selection, enrollment status)
    
    // Kunwari 5k ang Full Payment
    // array(1, 5000, 3.0, 1, 1, 1), // 1, 3
    // array(1, 5000, 4.5, 1, 0, 1), // 2
    // array(1, 5000, 3.5, 0, 1, 0), // 4, 6
    // array(1, 5000, 4.0, 0, 0, 0), // 5
    // array(1, 5000, 2.0, 0, 1, 0), // 7, 9
    // array(1, 5000, 1.5, 0, 0, 0), // 8
    // array(1, 2000, 4.0, 0, 1, 0), // 10, 12
    // array(1, 4000, 3.5, 0, 0, 0), // 11
    // array(1, 3000, 1.0, 0, 1, 0), // 13, 15
    // array(1, 2000, 2.0, 0, 0, 0), // 14
    // array(0, 5000, 4.0, 0, 1, 0), // 16, 18
    // array(0, 5000, 4.0, 0, 0, 0), // 17
    // array(0, 5000, 1.0, 0, 1, 0), // 19, 21
    // array(0, 5000, 2.0, 0, 0, 0), // 20
    // array(0, 4000, 4.0, 0, 1, 0), // 22, 24
    // array(0, 3000, 4.0, 0, 0, 0), // 23
    // array(0, 2000, 1.5, 0, 1, 0), // 25, 27
    // array(0, 1000, 4.0, 0, 0, 0), // 26

    array(1, 1, 3.0, 1, 1, 1), // 2
    array(1, 1, 4.5, 1, 0, 1), // 3
    array(1, 1, 3.0, 1, 2, 1), // 4
    array(1, 1, 3.5, 0, 1, 0), // 5
    array(1, 1, 4.0, 0, 0, 0), // 6
    array(1, 1, 3.5, 0, 2, 0), // 7
    array(1, 1, 2.0, 1, 2, 0), // 8
    array(1, 1, 1.5, 0, 2, 0), // 9
    array(1, 0, 4.0, 0, 1, 0), // 10
    array(1, 0, 3.5, 0, 0, 0), // 11
    array(1, 0, 4.0, 0, 2, 0), // 12
    array(1, 0, 1.0, 0, 2, 0), // 13
    array(0, 1, 3.0, 1, 1, 1), // 14
    array(0, 1, 4.0, 1, 0, 1), // 15
    array(0, 1, 4.5, 1, 2, 0), // 16
    array(0, 1, 3.0, 0, 1, 0), // 17
    array(0, 1, 3.5, 0, 0, 0), // 18
    array(0, 1, 3.0, 0, 2, 0), // 19
    array(0, 1, 2.0, 1, 2, 0), // 20
    array(0, 1, 2.0, 0, 2, 0), // 21
    array(0, 0, 3.0, 0, 1, 0), // 22
    array(0, 0, 3.5, 0, 0, 0), // 23
    array(0, 0, 3.0, 0, 2, 0), // 24
    array(0, 0, 2.0, 0, 2, 0), // 25
);

// Build decision tree

$decisionTree = buildDecisionTree($data, 5, 2, 0);

// Sample test data
if($_SERVER['REQUEST_METHOD']=="POST") {
    $sampleData = array(
                    $_POST['documents'],
                    $_POST['tuition'],
                    $_POST['grade'],
                    $_POST['clearance'],
                    $_POST['cs']); // Student's features (documents, tuition fee, grades, clearance)

    
// Predict
$prediction = predict($decisionTree, $sampleData);
$predictionResult = $prediction ? "Eligible" : "Not Eligible";

header("Location: ./index.php?prediction=$predictionResult");
exit;

}

?>

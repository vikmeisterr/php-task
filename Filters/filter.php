<?php
if (isset($_POST["submitbtn"])) {
    $reviewsJson = file_get_contents('reviews-2-2-2.json');
    $reviews = json_decode($reviewsJson, true);

    $textFilter = $_POST['textFilter'];
    $ratingOrder = $_POST['ratingOrder'];
    $dateOrder = $_POST['dateOrder'];
    $minRating = $_POST['minRating'];

    $filteredReviews = array_filter($reviews, function($review) use ($minRating) {
        return $review['rating'] >= $minRating;
    });

    usort($filteredReviews, function($a, $b) use ($textFilter, $ratingOrder, $dateOrder) {
        if ($textFilter == 'yes') {
            $aHasText = !empty($a['reviewText']);
            $bHasText = !empty($b['reviewText']);

            if ($aHasText && !$bHasText) {
                return -1;
            } elseif (!$aHasText && $bHasText) {
                return 1;
            }
        }

        $aDate = isset($a['date']) ? $a['date'] : '';
        $bDate = isset($b['date']) ? $b['date'] : '';

        if ($ratingOrder == 'highest') {
            return $b['rating'] - $a['rating'];
        } elseif ($ratingOrder == 'lowest') {
            return $a['rating'] - $b['rating'];
        }

        $aTimestamp = strtotime($aDate);
        $bTimestamp = strtotime($bDate);

        if ($dateOrder == 'newest') {
            return ($bTimestamp > $aTimestamp) ? 1 : -1;
        } elseif ($dateOrder == 'oldest') {
            return ($aTimestamp > $bTimestamp) ? 1 : -1;
        }
    });

    echo "<html><head><link rel='stylesheet' type='text/css' href='styles.css'></head><body><table id='output'>";
    foreach ($filteredReviews as $review) {
        echo '<tr>';
        echo '<td>' . $review['rating'] . '</td>';
        echo '<td>' . (isset($review['date']) ? $review['date'] : '') . '</td>';
        echo '<td>' . $review['reviewText'] . '</td>';
        echo '</tr>';
    }
    echo '</table></body><>';
}
?>




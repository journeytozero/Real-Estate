<?php include 'includes/header.php'; ?>
<?php require_once __DIR__ . "/admin/partials/db.php"; ?>

<!-- Hero Section -->
<section class="py-5 bg-light text-center">
    <div class="container">
        <h4 class="fw-bold text-primary">Available Properties</h4>
        <p class="text-muted">Browse through our latest listings and find your dream home.</p>
    </div>
</section>

<!-- Property List Section -->
<div class="container my-5">
    <div id="propertiesContainer" class="row g-4">
        <!-- Properties will load here dynamically -->
    </div>

    <!-- Load More Button -->
    <div class="text-center mt-4">
        <button id="loadMoreBtn" class="btn btn-primary btn-lg" style="display:none;">Load More</button>
    </div>
</div>

<?php //include 'includes/footer.php'; ?>

<!-- jQuery + AJAX Script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let page = 1;

function loadProperties(reset = false) {
    $.ajax({
        url: "fetch_propeties.php",
        method: "GET",
        data: { page: page },
        dataType: "json", // ✅ parse JSON properly
        beforeSend: function(){
            $("#loadMoreBtn").prop("disabled", true).text("Loading...");
        },
        success: function(response){
            if (reset) {
                $("#propertiesContainer").html(response.html);
            } else {
                $("#propertiesContainer").append(response.html);
            }

            if (response.hasMore) {
                $("#loadMoreBtn").show().prop("disabled", false).text("Load More");
            } else {
                $("#loadMoreBtn").hide();
            }
        }
    });
}

$(document).ready(function(){
    // Initial Load
    loadProperties(true);

    // Load More button
    $("#loadMoreBtn").click(function(){
        page++;
        loadProperties();
    });
});
</script>

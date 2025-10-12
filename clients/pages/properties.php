<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../partials/navbar.php';
?>

<div class="content">
    <h4 class="mb-4">Available Properties</h4>

    <!-- Compare Bar -->
    <div id="compareBar" class="alert alert-info d-none">
        <span id="compareCount">0</span> selected —
        <button id="compareBtn" class="btn btn-sm btn-primary">Compare</button>
    </div>

    <!-- Properties will be loaded here -->
    <div id="propertiesContainer" class="row g-4"></div>

    <!-- Load More Button -->
    <div class="text-center mt-4">
        <button id="loadMoreBtn" class="btn btn-primary" style="display:none;">Load More</button>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let page = 1;
let selected = [];

// ✅ Load properties
function loadProperties(reset = false) {
    $.ajax({
        url: "../fetch_properties.php",
        method: "GET",
        data: { page },
        dataType: "json",
        beforeSend: () => $("#loadMoreBtn").prop("disabled", true).text("Loading..."),
        success: function(res) {
            if (reset) $("#propertiesContainer").html(res.html);
            else $("#propertiesContainer").append(res.html);

            if (res.hasMore) {
                $("#loadMoreBtn").show().prop("disabled", false).text("Load More");
            } else {
                $("#loadMoreBtn").hide();
            }
        }
    });
}

// ✅ Save property (calls correct file)
$(document).on("click", ".save-btn", function() {
    let id = $(this).data("id");
    let btn = $(this);

    $.post("../save_property.php", { property_id: id }, function(res) {
        if (res === "login") {
            alert("Please login to save properties.");
            window.location.href = "../login.php";
        } else if (res === "saved") {
            btn.removeClass("btn-outline-danger").addClass("btn-danger");
        } else if (res === "unsaved") {
            btn.removeClass("btn-danger").addClass("btn-outline-danger");
        }
    });
});

// ✅ Compare checkbox
$(document).on("change", ".compare-check", function() {
    let id = $(this).data("id");
    if (this.checked) {
        if (selected.length < 3) {
            selected.push(id);
        } else {
            alert("You can only compare up to 3 properties.");
            this.checked = false;
        }
    } else {
        selected = selected.filter(x => x != id);
    }
    $("#compareCount").text(selected.length);
    $("#compareBar").toggleClass("d-none", selected.length === 0);
});

// ✅ Compare button
$("#compareBtn").click(function() {
    if (selected.length < 2) {
        alert("Select at least 2 properties to compare.");
        return;
    }
    window.location.href = "compare.php?ids=" + selected.join(",");
});

// ✅ Init
$(document).ready(function() {
    loadProperties(true);
    $("#loadMoreBtn").click(function() {
        page++;
        loadProperties();
    });
});
</script>

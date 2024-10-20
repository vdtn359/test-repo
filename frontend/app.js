$(document).ready(function () {
    const $searchBar = $("#searchBar");
    const $tableBody = $("#tableBody");
    let enrolmentsData = [];  // holds the data fetched from backend

    async function fetchData() {
        try {
            // fetch the data from backend
            const response = await fetch("http://localhost/MindAtlas PHP Test/php/get_data.php");

            if (!response.ok) {
                throw new Error("HTTP Error, status: " + response.status);
            }

            enrolmentsData = await response.json();
            displayEnrolments(enrolmentsData);  // display all enrolments
        } catch (error) {
            console.error("Error when fetching the enrolments: ", error);
        }
    }

    fetchData();

    // Safely display enrolments in the table by avoiding HTML injection
    function displayEnrolments(enrolments) {
        $tableBody.empty();  // clear table content
        enrolments.forEach(enrolment => {
            const $row = $("<tr></tr>");

            // Create table cells and set their text safely
            const $nameCell = $("<td></td>").text(`${enrolment.firstname} ${enrolment.surname}`);
            const $descriptionCell = $("<td></td>").text(enrolment.description);
            const $statusCell = $("<td></td>").text(enrolment.completion_status);

            // Append the cells to the row
            $row.append($nameCell, $descriptionCell, $statusCell);

            // Append the row to the table body
            $tableBody.append($row);
        });
    }

    // Event listener for search input
    $searchBar.on("input", function () {
        const query = $(this).val().toLowerCase();
        const filteredEnrolments = enrolmentsData.filter(enrolment => {
            return enrolment.firstname.toLowerCase().includes(query) ||
                   enrolment.surname.toLowerCase().includes(query) ||
                   enrolment.description.toLowerCase().includes(query);
        });
        displayEnrolments(filteredEnrolments); // display filtered results
    });
});

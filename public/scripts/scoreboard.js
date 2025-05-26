/**
 * javascript for the public scoreboard
 * fetches scoreboard data from get_scores.php and updates the scoreboard
 * updates the scoreboard every 5 seconds
 */

const scoreboard = document.getElementById("scoreboard-body");
const loadingIndicator = document.getElementById("loading-indicator");

/**
 * fetches the scoreboard data from get_scores.php
 * @returns {Promise<Array>} a promise that resolves with the scoreboard data
 */
async function fetchScoreboardData() {
    try {
        const response = await fetch("get_scores.php");
        if (!response.ok) {
            throw new Error("HTTP error! status: " + response.status);
        }
        const result = await response.json();
        if (result.success) {
            return result.data;
        } else {
            console.error("Error fetching scoreboard data:", result.message);
            return [];
        }
    } catch (error) {
        console.error("Error fetching scoreboard data:", error);
        if (scoreboard) {
            scoreboard.innerHTML = "<tr><td colspan='3'>Error fetching scoreboard data</td></tr>";
        }
        return [];
    }
}

/**
 * renders the scoreboard with the given data
 * @param {Array} data - the scoreboard data
 */
function renderScoreboard(data) {
    if (scoreboard) {
        scoreboard.innerHTML = "";
    }

    if (loadingIndicator) {
        loadingIndicator.style.display = "none";
    }

    if(!data || data.length === 0) {
        if (scoreboard) {
            scoreboard.innerHTML = "<tr><td colspan='3'>No scores available</td></tr>";
        }
        return;
    }

    data.forEach((entry, index) => {
        const row = document.createElement("tr");
        if (index === 0) {
            row.classList.add("highlight");
        }

        const rankCell = document.createElement("td");
        rankCell.textContent = index + 1;

        const nameCell = document.createElement("td");
        nameCell.textContent = entry.display_name;

        const pointsCell = document.createElement("td");
        pointsCell.textContent = entry.total_points;

        row.appendChild(rankCell);
        row.appendChild(nameCell);
        row.appendChild(pointsCell);

        if (scoreboard) {
            scoreboard.appendChild(row);
        }
    })
}

/**
 * updates the scoreboard
 * fetches the scoreboard data and renders it
 */
async function updateScoreboard() {
    const data = await fetchScoreboardData();
    renderScoreboard(data);
}

document.addEventListener("DOMContentLoaded", updateScoreboard);
setInterval(updateScoreboard, 5000); // update every 5 seconds
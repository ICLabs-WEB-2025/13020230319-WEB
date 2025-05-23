let currentSimNumber = null;
let lastMessageCount = 0;

function selectSim(simNumber) {
    currentSimNumber = simNumber;
    sessionStorage.setItem("current_sim", simNumber);
    document
        .querySelectorAll(".sim-item")
        .forEach((item) => item.classList.remove("active"));
    document
        .querySelector(`.sim-item[data-sim="${simNumber}"]`)
        .classList.add("active");
    document.getElementById("chatInput").disabled = false;
    document.getElementById("chatInput").focus();
    document.querySelector("#chatForm button").disabled = false;
    document.getElementById("chatSimNumber").value = simNumber;
    document.getElementById(
        "chatHeaderTitle"
    ).innerHTML = `Chat dengan ${simNumber} <span class="clear-session" onclick="clearSimSession()" id="clearSessionBtn">Hapus Sesi</span>`;
    document.getElementById("clearSessionBtn").style.display = "inline";
    loadMessages();
    console.log("Selected SIM:", simNumber);
}

function clearSimSession() {
    currentSimNumber = null;
    sessionStorage.removeItem("current_sim");
    document
        .querySelectorAll(".sim-item")
        .forEach((item) => item.classList.remove("active"));
    document.getElementById("chatInput").disabled = true;
    document.querySelector("#chatForm button").disabled = true;
    document.getElementById("chatSimNumber").value = "";
    document.getElementById("chatHeaderTitle").innerHTML =
        "Pilih Pemegang SIM untuk memulai chat";
    document.getElementById("clearSessionBtn").style.display = "none";
    document.getElementById("chatMessages").innerHTML =
        '<div class="text-muted text-center p-3">Pilih Pemegang SIM untuk memulai chat.</div>';
}

document.getElementById("chatForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const message = document.getElementById("chatInput").value.trim();
    const simNumber = document.getElementById("chatSimNumber").value;
    if (message && simNumber) {
        console.log("Sending message:", { message, sim_number: simNumber });
        fetch('{{ route("chat.send") }}', {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify({ message, sim_number: simNumber }),
        })
            .then((response) => {
                console.log("Response status:", response.status);
                if (!response.ok)
                    throw new Error("Network response was not ok");
                return response.json();
            })
            .then((data) => {
                console.log("Response data:", data);
                if (data.success) {
                    document.getElementById("chatInput").value = "";
                    loadMessages();
                } else {
                    alert("Gagal mengirim pesan: " + data.message);
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Gagal mengirim pesan: " + error.message);
            });
    }
});

function loadMessages() {
    if (!currentSimNumber) return;
    console.log("Loading messages for:", currentSimNumber);
    fetch(`{{ route("chat.get") }}?sim_number=${currentSimNumber}`, {
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
    })
        .then((response) => {
            console.log("Fetch status:", response.status);
            if (!response.ok) throw new Error("Network response was not ok");
            return response.json();
        })
        .then((data) => {
            console.log("Fetched messages:", data);
            const chatMessages = document.getElementById("chatMessages");
            const newMessages = data
                .map((msg) => {
                    const date = new Date(msg.created_at);
                    return `
                <div class="message ${
                    msg.sender_type === "admin" ? "admin" : "user"
                }">
                    <strong>${
                        msg.sender_type === "admin" ? "Admin" : "Pemegang SIM"
                    }:</strong> ${e(msg.message)}
                    <div class="time">${date.toLocaleString()}</div>
                </div>
            `;
                })
                .join("");
            chatMessages.innerHTML = newMessages;
            if (data.length > lastMessageCount) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
                lastMessageCount = data.length;
            }
        })
        .catch((error) => console.error("Error loading messages:", error));
}

function e(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

setInterval(loadMessages, 3000);
const savedSim = sessionStorage.getItem("current_sim");
if (savedSim) selectSim(savedSim);

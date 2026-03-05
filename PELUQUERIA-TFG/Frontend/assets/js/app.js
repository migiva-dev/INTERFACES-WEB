async function loadSlots() {
  const serviceId = document.querySelector("#service_id")?.value;
  const date = document.querySelector("#date")?.value;
  const slotsBox = document.querySelector("#slots");
  const msg = document.querySelector("#msg");

  if (!serviceId || !date || !slotsBox) return;
  slotsBox.innerHTML = "Cargando…";
  msg.innerHTML = "";

  try {
    // Endpoint backend
    const url = `../Backend/api/availability.php?service_id=${encodeURIComponent(serviceId)}&date=${encodeURIComponent(date)}`;
    const res = await fetch(url);
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || "Error");

    if (!data.slots.length) {
      slotsBox.innerHTML = "<small>No hay huecos disponibles.</small>";
      return;
    }

    slotsBox.innerHTML = "";
    const wrap = document.createElement("div");
    wrap.className = "slot-wrap";

    data.slots.forEach((iso) => {
      const d = new Date(iso);
      const hhmm = d.toLocaleTimeString("es-ES", { hour: "2-digit", minute: "2-digit" });

      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "slot";
      btn.textContent = hhmm;

      btn.onclick = () => {
        document.querySelector("#start_datetime").value = iso;
        document.querySelector("#bookForm").submit();
      };

      wrap.appendChild(btn);
    });

    slotsBox.appendChild(wrap);
  } catch (e) {
    slotsBox.innerHTML = "";
    msg.innerHTML = `<div class="error">${e.message}</div>`;
  }
}

window.addEventListener("DOMContentLoaded", () => {
  document.querySelector("#service_id")?.addEventListener("change", loadSlots);
  document.querySelector("#date")?.addEventListener("change", loadSlots);
  loadSlots();
});
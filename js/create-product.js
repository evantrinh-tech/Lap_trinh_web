/* ============================================================
   create-product.js
   JS cho trang thêm sản phẩm admin
============================================================ */

/* ── IMAGE SLOTS ─────────────────────── */
const imageFiles = [null, null, null, null];
const imagePreviews = ["", "", "", ""];
const slotsEl = document.getElementById("image-slots");

const fileInput = document.createElement("input");
fileInput.type = "file";
fileInput.accept = "image/*";
let currentSlot = 0;

fileInput.addEventListener("change", () => {
  const file = fileInput.files[0];
  if (!file) return;
  if (imagePreviews[currentSlot])
    URL.revokeObjectURL(imagePreviews[currentSlot]);
  imageFiles[currentSlot] = file;
  imagePreviews[currentSlot] = URL.createObjectURL(file);
  renderSlots();
  fileInput.value = "";
});

function renderSlots() {
  slotsEl.innerHTML = "";
  imagePreviews.forEach((url, i) => {
    const slot = document.createElement("div");
    slot.className =
      "img-slot border-2 border-dashed border-outline-variant/40 rounded-xl overflow-hidden cursor-pointer bg-surface-container relative group";
    slot.style.aspectRatio = "1";
    slot.innerHTML = url
      ? `<img src="${url}" class="w-full h-full object-cover" />
         <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
           <span class="material-symbols-outlined text-white text-2xl">edit</span>
         </div>
         ${i === 0 ? '<span class="absolute top-2 left-2 bg-primary text-white text-[10px] font-bold px-2 py-0.5 rounded-full">Chính</span>' : ""}
         <button type="button" onclick="removeImage(${i})" class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold opacity-0 group-hover:opacity-100 transition-opacity">×</button>`
      : `<div class="w-full h-full flex flex-col items-center justify-center gap-1 text-on-surface-variant">
           <span class="material-symbols-outlined text-2xl opacity-40">add_photo_alternate</span>
           <span class="text-[11px] opacity-50 font-medium">${i === 0 ? "Ảnh chính" : "Ảnh " + (i + 1)}</span>
         </div>`;
    slot.addEventListener("click", (e) => {
      if (e.target.tagName === "BUTTON") return;
      currentSlot = i;
      fileInput.click();
    });
    slotsEl.appendChild(slot);
  });
}

window.removeImage = (i) => {
  if (imagePreviews[i]) URL.revokeObjectURL(imagePreviews[i]);
  imageFiles[i] = null;
  imagePreviews[i] = "";
  renderSlots();
};

renderSlots();

/* ── COLOR VARIANTS ──────────────────── */
let colors = [];
const colorTagsEl = document.getElementById("color-tags");
const colorNameIn = document.getElementById("color-name-input");
const colorHexIn = document.getElementById("color-hex-input");
const colorHexText = document.getElementById("color-hex-text");
const colorStockIn = document.getElementById("color-stock-input");
const colorSizeIn = document.getElementById("color-size-input");

colorHexIn.addEventListener("input", () => {
  colorHexText.value = colorHexIn.value;
});
colorHexText.addEventListener("input", () => {
  if (/^#[0-9a-fA-F]{6}$/.test(colorHexText.value))
    colorHexIn.value = colorHexText.value;
});

function renderColorTags() {
  colorTagsEl.innerHTML = "";
  if (!colors.length) {
    colorTagsEl.innerHTML =
      '<span style="font-size:12px;color:#888;font-style:italic;">Chưa có màu nào...</span>';
    return;
  }
  colors.forEach((c, i) => {
    const tag = document.createElement("span");
    tag.className = "color-tag";
    tag.innerHTML = `
      <span style="width:14px;height:14px;border-radius:50%;background:${c.color_hex};display:inline-block;border:1.5px solid rgba(0,0,0,.15);flex-shrink:0;"></span>
      <span>${c.color}</span>
      <span style="color:#999;font-size:11px;">${c.size || ""} · ${c.stock}pcs</span>
      <button onclick="removeColor(${i})">×</button>`;
    colorTagsEl.appendChild(tag);
  });
}
renderColorTags();

window.removeColor = (i) => {
  colors.splice(i, 1);
  renderColorTags();
};

function addColor() {
  const name = colorNameIn.value.trim();
  if (!name) {
    colorNameIn.focus();
    showToast("Nhập tên màu", "error");
    return;
  }
  if (colors.find((c) => c.color.toLowerCase() === name.toLowerCase())) {
    showToast("Màu đã tồn tại", "error");
    return;
  }
  colors.push({
    color: name,
    color_hex: colorHexIn.value,
    stock: parseInt(colorStockIn.value) || 0,
    size: colorSizeIn.value.trim(),
  });
  renderColorTags();
  colorNameIn.value = "";
  colorSizeIn.value = "";
}

document.getElementById("add-color-btn").addEventListener("click", addColor);
colorNameIn.addEventListener("keydown", (e) => {
  if (e.key === "Enter") {
    e.preventDefault();
    addColor();
  }
});
document.querySelectorAll(".preset-btn").forEach((btn) => {
  btn.addEventListener("click", () => {
    colorNameIn.value = btn.dataset.color;
    colorHexIn.value = btn.dataset.hex;
    colorHexText.value = btn.dataset.hex;
  });
});

/* ── SLUG AUTO ───────────────────────── */
document.getElementById("name").addEventListener("input", function () {
  const slugEl = document.getElementById("slug");
  if (!slugEl.dataset.manual) {
    slugEl.value = this.value
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .replace(/đ/g, "d")
      .replace(/[^a-z0-9\s-]/g, "")
      .trim()
      .replace(/\s+/g, "-");
  }
});
document.getElementById("slug").addEventListener("input", function () {
  this.dataset.manual = "true";
});

/* ── RESET ───────────────────────────── */
function resetForm() {
  document.getElementById("createProductForm").reset();
  colors = [];
  imagePreviews.forEach((url, i) => {
    if (url) URL.revokeObjectURL(url);
    imagePreviews[i] = "";
    imageFiles[i] = null;
  });
  renderColorTags();
  renderSlots();
  delete document.getElementById("slug").dataset.manual;
}
document.getElementById("reset-btn").addEventListener("click", resetForm);

/* ── SUBMIT → FormData ───────────────── */
document
  .getElementById("createProductForm")
  .addEventListener("submit", async function (e) {
    e.preventDefault();

    const validFiles = imageFiles.filter(Boolean);
    if (!validFiles.length) {
      showToast("Cần ít nhất 1 hình ảnh", "error");
      return;
    }

    const btn = document.getElementById("submit-btn");
    btn.disabled = true;
    btn.innerHTML = '<span class="animate-pulse font-bold">Đang lưu...</span>';

    try {
      const formData = new FormData();

      // Gắn từng file ảnh
      imageFiles.forEach((file) => {
        if (file) formData.append("files[]", file);
      });

      // Gắn data JSON
      formData.append(
        "data",
        JSON.stringify({
          sku: document.getElementById("sku").value.trim(),
          slug: document.getElementById("slug").value.trim(),
          name: document.getElementById("name").value.trim(),
          brand: document.getElementById("brand").value,
          gender: document.getElementById("gender").value,
          frame_material: document
            .getElementById("frame_material")
            .value.trim(),
          lens_type: document.getElementById("lens_type").value.trim(),
          price: parseFloat(document.getElementById("price").value) || 0,
          badge: document.getElementById("badge").value.trim(),
          short_description: document
            .getElementById("short_description")
            .value.trim(),
          description: document.getElementById("description").value.trim(),
          is_active: true,
          is_featured: false,
          variants: colors,
        }),
      );

      const token = localStorage.getItem("kx_auth_token");
      const res = await fetch("/backend/api/products/create-product.php", {
        method: "POST",
        headers: { Authorization: `Bearer ${token}` },
        body: formData,
      });

      const data = await res.json();
      if (data.success) {
        showToast("Thêm sản phẩm thành công!", "success");
        resetForm();
      } else {
        showToast(data.message || "Lỗi khi thêm sản phẩm", "error");
      }
    } catch {
      showToast("Lỗi kết nối server", "error");
    } finally {
      btn.disabled = false;
      btn.innerHTML =
        '<span class="material-symbols-outlined text-base">add_circle</span> Thêm sản phẩm';
    }
  });

/* ── TOAST ───────────────────────────── */
function showToast(msg, type = "info") {
  document.getElementById("kx-toast")?.remove();
  const c = {
    error: "background:#ef4444;color:#fff",
    success: "background:#16a34a;color:#fff",
    info: "background:#002366;color:#fff",
  };
  const t = Object.assign(document.createElement("div"), {
    id: "kx-toast",
    textContent: msg,
  });
  t.setAttribute(
    "style",
    `position:fixed;top:24px;right:24px;z-index:9999;padding:12px 20px;border-radius:12px;font-weight:600;font-size:14px;box-shadow:0 8px 24px rgba(0,0,0,.15);opacity:0;transition:opacity .3s,transform .3s;transform:translateY(-8px);${c[type]}`,
  );
  document.body.appendChild(t);
  requestAnimationFrame(() => {
    t.style.opacity = "1";
    t.style.transform = "translateY(0)";
  });
  setTimeout(() => {
    t.style.opacity = "0";
    setTimeout(() => t.remove(), 300);
  }, 3000);
}

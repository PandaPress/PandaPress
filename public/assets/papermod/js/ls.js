const ls = {
  getAll() {
    const settings = localStorage.getItem("PandaPress.papermod") || "{}";
    return JSON.parse(settings);
  },
  get(key) {
    try {
      const settings = ls.getAll();
      return settings[key] || {};
    } catch (e) {
      console.log("e :>> ", e);
    }
    return {};
  },
  set(key, value) {
    const settings = ls.getAll();
    settings[key] = value; // could be any type
    localStorage.setItem("PandaPress.papermod", JSON.stringify(settings));
  },

  remove(key) {
    const settings = ls.getAll();
    delete settings[key];
    localStorage.setItem("PandaPress.papermod", JSON.stringify(settings));
  },
  clear() {
    localStorage.removeItem("PandaPress.papermod");
  },
};

window._ls = ls;

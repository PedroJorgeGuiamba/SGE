class SessionManager {
  constructor(options = {}) {
    // Configurações com valores padrão (nada de inline JS!)
    this.config = {
      timeoutMinutes: options.timeoutMinutes || 30, // 30 minutos de inatividade
      heartbeatInterval: options.heartbeatInterval || 60, // Heartbeat a cada 60 segundos
      warningMinutes: options.warningMinutes || 2, // Avisar 2 minutos antes
      heartbeatUrl: options.heartbeatUrl || "/estagio/api/heartbeat-beacon",
      loginUrl: options.loginUrl || "/estagio/login",
    };

    this.lastActivity = Date.now();
    this.lastHeartbeat = Date.now();
    this.warningShown = false;
    this.logoutInProgress = false;

    this.init();
  }

  init() {
    // Atualiza atividade em interações do usuário
    const events = ["mousemove", "keypress", "click", "scroll", "touchstart"];
    events.forEach((event) => {
      document.addEventListener(event, () => this.updateActivity());
    });

    // Inicia heartbeat periódico
    setInterval(() => this.checkHeartbeat(), 1000);

    // Verifica inatividade a cada 30 segundos
    setInterval(() => this.checkInactivity(), 30000);

    // Configura beacon para fechamento da página
    this.setupBeacon();

    // Detecta quando a página fica visível/invisível
    document.addEventListener("visibilitychange", () =>
      this.handleVisibilityChange(),
    );

    // Recupera última atividade do localStorage (para múltiplas abas)
    this.syncActivityAcrossTabs();

    console.log(
      `SessionManager inicializado (timeout: ${this.config.timeoutMinutes}min, heartbeat: ${this.config.heartbeatInterval}s)`,
    );
  }

  updateActivity() {
    this.lastActivity = Date.now();
    this.warningShown = false;

    // Salva em localStorage para sincronizar entre abas
    localStorage.setItem("lastActivity", this.lastActivity);

    // Força heartbeat imediato se passou muito tempo
    if (Date.now() - this.lastHeartbeat > 60000) {
      this.sendHeartbeat();
    }
  }

  syncActivityAcrossTabs() {
    // Escuta mudanças no localStorage (outras abas)
    window.addEventListener("storage", (e) => {
      if (e.key === "lastActivity") {
        const otherActivity = parseInt(e.newValue);
        if (otherActivity > this.lastActivity) {
          this.lastActivity = otherActivity;
        }
      }
    });

    // Atualiza localStorage periodicamente
    setInterval(() => {
      localStorage.setItem("lastActivity", this.lastActivity);
    }, 5000);
  }

  handleVisibilityChange() {
    if (document.hidden) {
      console.log("Página minimizada/inativa");
    } else {
      // Página voltou a ficar visível - atualiza atividade
      this.updateActivity();
      this.sendHeartbeat();
    }
  }

  async sendHeartbeat() {
    if (this.logoutInProgress) return;

    const now = Date.now();
    if (now - this.lastHeartbeat < 30000) return; // No mínimo 30 segundos entre heartbeats

    try {
      const response = await fetch(this.config.heartbeatUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({ type: "heartbeat" }),
        credentials: "same-origin",
      });

      if (response.ok) {
        this.lastHeartbeat = now;
        console.log("Heartbeat enviado com sucesso");
      } else if (response.status === 401) {
        this.handleSessionExpired();
      }
    } catch (error) {
      console.error("Erro no heartbeat:", error);
      // Se falhou, tenta novamente em 30 segundos
      setTimeout(() => this.sendHeartbeat(), 30000);
    }
  }

  checkHeartbeat() {
    // Envia heartbeat a cada intervalo configurado
    if (
      Date.now() - this.lastHeartbeat >=
      this.config.heartbeatInterval * 1000
    ) {
      this.sendHeartbeat();
    }
  }

  checkInactivity() {
    const inactiveTime = Date.now() - this.lastActivity;
    const timeoutMs = this.config.timeoutMinutes * 60 * 1000;

    if (inactiveTime >= timeoutMs) {
      console.log("Inatividade detectada, encerrando sessão");
      this.endSessionBeacon();
      window.location.href = this.config.loginUrl + "?timeout=1";
    } else if (
      !this.warningShown &&
      inactiveTime >= timeoutMs - this.config.warningMinutes * 60 * 1000
    ) {
      this.warningShown = true;
      this.showWarningModal();
    }
  }

  showWarningModal() {
    const remaining = this.config.warningMinutes;
    const message = `Sua sessão expirará em ${remaining} minuto(s) por inatividade.`;

    // Usa um modal mais amigável (fallback para confirm)
    if (confirm(message + "\n\nDeseja continuar conectado?")) {
      this.updateActivity();
      this.sendHeartbeat();
    }
  }

  setupBeacon() {
    window.addEventListener("beforeunload", () => {
      // Envia beacon para registrar saída (navegação/fechar aba)
      // Não encerra a sessão - apenas registra o timestamp
      if (this.logoutInProgress) return;

      const blob = new Blob([JSON.stringify({ type: "unload" })], {
        type: "application/json",
      });
      navigator.sendBeacon(this.config.heartbeatUrl, blob);
    });

    window.addEventListener("offline", () => {
      console.log("Conexão perdida. Sessão será mantida até reconexão");
      localStorage.setItem("wasOffline", "true");
    });

    window.addEventListener("online", () => {
      if (localStorage.getItem("wasOffline") === "true") {
        console.log("Reconectado, verificando sessão...");
        this.sendHeartbeat();
        localStorage.removeItem("wasOffline");
      }
    });
  }

  endSessionBeacon() {
    this.logoutInProgress = true;
    // Notifica servidor que expiraram por timeout
    // Usa fetch para garantir a resposta antes do redirect
    const blob = new Blob([JSON.stringify({ type: "timeout" })], {
      type: "application/json",
    });

    // Tenta ambos: fetch para resposta imediata, beacon como fallback
    try {
      fetch(this.config.heartbeatUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({ type: "timeout" }),
        keepalive: true,
        credentials: "same-origin",
      }).catch(() => {
        // Fallback para beacon se fetch falhar
        navigator.sendBeacon(this.config.heartbeatUrl, blob);
      });
    } catch (error) {
      navigator.sendBeacon(this.config.heartbeatUrl, blob);
    }
  }

  handleSessionExpired() {
    console.log("Sessão expirada no servidor");
    this.logoutInProgress = true;
    window.location.href = this.config.loginUrl + "?session_expired=1";
  }
}

(function () {
  function initSessionManager() {
    if (!window.sessionManager) {
      // Pega configurações de um atributo data se existir
      const scriptTag = document.querySelector("script[data-session-config]");
      let options = {};

      if (scriptTag && scriptTag.dataset.sessionConfig) {
        try {
          options = JSON.parse(scriptTag.dataset.sessionConfig);
        } catch (e) {}
      }

      window.sessionManager = new SessionManager(options);
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initSessionManager);
  } else {
    initSessionManager();
  }
})();

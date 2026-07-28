import { describe, expect, it } from "vitest";
import api, {
  buildRequestConfig,
  createAbortController,
  getApiErrorMessage,
  setAuthToken,
} from "./client";

describe("api client helpers", () => {
  it("sets and clears the auth header", () => {
    setAuthToken("demo-token");
    expect(api.defaults.headers.common.Authorization).toBe("Bearer demo-token");

    setAuthToken(null);
    expect(api.defaults.headers.common.Authorization).toBeUndefined();
  });

  it("includes an abort signal in request config when provided", () => {
    const controller = createAbortController();
    const config = buildRequestConfig(controller);

    expect(config.signal).toBe(controller.signal);
    controller.abort();
  });

  it("extracts a server message from axios errors and falls back otherwise", () => {
    const fallback = "Request failed";
    const axiosError = {
      isAxiosError: true,
      response: {
        data: { message: "Email already taken" },
      },
    };

    expect(getApiErrorMessage(axiosError, fallback)).toBe("Email already taken");
    expect(getApiErrorMessage(new Error("boom"), fallback)).toBe(fallback);
  });
});

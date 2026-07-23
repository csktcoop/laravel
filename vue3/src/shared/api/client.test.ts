import { describe, expect, it } from "vitest";
import api, {
  buildRequestConfig,
  createAbortController,
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
});

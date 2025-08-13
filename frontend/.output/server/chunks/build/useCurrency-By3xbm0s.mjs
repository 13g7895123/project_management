function formatCurrency(amount, options = {}) {
  const {
    currency = "NT$",
    minimumFractionDigits = 0,
    maximumFractionDigits = 0,
    showZero = true
  } = options;
  if (amount === null || amount === void 0 || amount === "") {
    return showZero ? `${currency}0` : `${currency}--`;
  }
  const numericAmount = typeof amount === "string" ? parseFloat(amount) : Number(amount);
  if (isNaN(numericAmount)) {
    return showZero ? `${currency}0` : `${currency}--`;
  }
  const formatted = numericAmount.toLocaleString("en-US", {
    minimumFractionDigits,
    maximumFractionDigits
  });
  return `${currency}${formatted}`;
}
const useCurrency = () => {
  const formatTWD = (amount) => {
    return formatCurrency(amount, { currency: "NT$" });
  };
  const formatTWDWithDecimals = (amount) => {
    return formatCurrency(amount, {
      currency: "NT$",
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  };
  const formatNumber = (amount) => {
    return formatCurrency(amount, { currency: "" });
  };
  const formatChartCurrency = (amount) => {
    return formatCurrency(amount, { currency: "NT$" });
  };
  return {
    formatTWD,
    formatTWDWithDecimals,
    formatNumber,
    formatChartCurrency,
    formatCurrency
  };
};

export { useCurrency as u };
//# sourceMappingURL=useCurrency-By3xbm0s.mjs.map

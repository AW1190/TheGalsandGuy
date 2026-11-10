import { Box, MenuItem, Select, Stack, Typography } from "@mui/material";
import type { DashboardData } from "./dashboard-data";
import { DashboardPanel } from "./dashboard-panel";

const money = new Intl.NumberFormat("en-GB", {
  style: "currency",
  currency: "GBP",
  maximumFractionDigits: 0,
});

export function RevenueOverview({
  revenue,
  total,
}: {
  revenue: DashboardData["monthlyRevenue"];
  total: number;
}) {
  const maxRevenue = Math.max(1000, ...revenue.map((item) => item.revenue));
  const axisMaximum = Math.ceil(maxRevenue / 1000) * 1000;
  const axisSteps = Array.from({ length: 5 }, (_, index) =>
    axisMaximum - (axisMaximum / 4) * index,
  );

  return (
    <DashboardPanel
      title="Revenue overview"
      subtitle="Your revenue performance over time"
      action={
        <Select size="small" defaultValue="week" aria-label="Revenue period" sx={{ minWidth: 112, height: 30, fontSize: 11 }}>
          <MenuItem value="week">This week</MenuItem>
          <MenuItem value="month">This month</MenuItem>
        </Select>
      }
    >
      <Stack sx={{ flexDirection: "row", alignItems: "baseline", gap: 1, mt: 2, mb: 1 }}>
        <Typography sx={{ fontSize: 20, fontWeight: 650 }}>{money.format(total)}</Typography>
        <Typography variant="caption" color="text.secondary">Revenue from active orders</Typography>
      </Stack>
      <Box sx={{ height: 175, display: "flex", gap: 1, pt: 1 }}>
        <Stack sx={{ justifyContent: "space-between", pb: 3, color: "#97a29b", fontSize: 10 }}>
          {axisSteps.map((step) => <span key={step}>{money.format(step)}</span>)}
        </Stack>
        <Box sx={{ flex: 1, minWidth: 0, display: "flex", alignItems: "stretch", justifyContent: "space-around", borderBottom: "1px solid #e8edea", background: "repeating-linear-gradient(to bottom, transparent 0, transparent 33px, #edf1ee 34px)" }}>
          {revenue.map((item, index) => (
            <Stack key={index} sx={{ alignItems: "center", justifyContent: "flex-end", width: "6%", height: "100%" }}>
              <Box sx={{ width: "65%", height: `${Math.max(0, (item.revenue / axisMaximum) * 100)}%`, maxWidth: 20, bgcolor: index === revenue.length - 1 ? "#287348" : "#d9ebe0", borderRadius: "3px 3px 0 0" }} />
            </Stack>
          ))}
        </Box>
      </Box>
      <Stack sx={{ flexDirection: "row", justifyContent: "space-around", ml: 3, mt: 0.75 }}>
        {revenue.map((item) => (
          <Typography key={item.month} variant="caption" color="text.secondary" sx={{ fontSize: 10 }}>{item.month}</Typography>
        ))}
      </Stack>
    </DashboardPanel>
  );
}

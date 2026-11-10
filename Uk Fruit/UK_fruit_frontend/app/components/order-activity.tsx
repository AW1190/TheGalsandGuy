import { Box, LinearProgress, Stack, Typography } from "@mui/material";
import type { DashboardData } from "./dashboard-data";
import { DashboardPanel } from "./dashboard-panel";

const activityColors: Record<string, string> = {
  active: "#43875b",
  cancelled: "#c17575",
};

export function OrderActivity({ activity }: { activity: DashboardData["orderActivity"] }) {
  const total = activity.reduce((sum, item) => sum + item.count, 0);

  return (
    <DashboardPanel title="Order activity" subtitle="Current fulfilment status">
      <Stack sx={{ flexDirection: "row", alignItems: "baseline", gap: 1, mt: 2, mb: 2 }}>
        <Typography sx={{ fontSize: 24, fontWeight: 650 }}>{total}</Typography>
        <Typography variant="caption" color="text.secondary">total orders</Typography>
      </Stack>
      <Stack sx={{ gap: 2 }}>
        {activity.map((item) => (
          <Box key={item.status}>
            <Stack sx={{ flexDirection: "row", justifyContent: "space-between", mb: 0.5 }}>
              <Typography variant="caption" color="text.secondary">
                {item.status.charAt(0).toUpperCase() + item.status.slice(1)}
              </Typography>
              <Typography variant="caption" sx={{ fontWeight: 600 }}>{item.count}</Typography>
            </Stack>
            <LinearProgress
              variant="determinate"
              value={total ? (item.count / total) * 100 : 0}
              sx={{ height: 6, borderRadius: 4, bgcolor: "#f0f3f1", "& .MuiLinearProgress-bar": { bgcolor: activityColors[item.status] ?? "#83a8bf", borderRadius: 4 } }}
            />
          </Box>
        ))}
      </Stack>
    </DashboardPanel>
  );
}

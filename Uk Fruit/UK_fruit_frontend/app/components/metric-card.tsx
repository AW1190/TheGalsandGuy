import type { ReactNode } from "react";
import { Box, Paper, Stack, Typography } from "@mui/material";

export function MetricCard({
  label,
  value,
  detail,
  icon,
  tone,
}: {
  label: string;
  value: string;
  detail: string;
  icon: ReactNode;
  tone: string;
}) {
  return (
    <Paper component="article" variant="outlined" sx={{ p: 2, borderColor: "#e5eae7", borderRadius: 2, minHeight: 126 }}>
      <Stack sx={{ flexDirection: "row", alignItems: "center", justifyContent: "space-between" }}>
        <Typography variant="body2" color="text.secondary">{label}</Typography>
        <Box sx={{ width: 34, height: 34, display: "grid", placeItems: "center", borderRadius: 1.5, color: tone, bgcolor: `${tone}14` }}>
          {icon}
        </Box>
      </Stack>
      <Typography sx={{ mt: 1, fontSize: 24, fontWeight: 650, color: "#28372d" }}>{value}</Typography>
      <Typography variant="caption" color="text.secondary">{detail}</Typography>
    </Paper>
  );
}

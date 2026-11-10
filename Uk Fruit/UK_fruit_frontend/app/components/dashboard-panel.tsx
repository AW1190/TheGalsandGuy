import type { ReactNode } from "react";
import { Box, Paper, Stack, Typography } from "@mui/material";

export function DashboardPanel({
  title,
  subtitle,
  action,
  children,
}: {
  title: string;
  subtitle?: string;
  action?: ReactNode;
  children: ReactNode;
}) {
  return (
    <Paper component="section" variant="outlined" sx={{ p: 2, borderColor: "#e5eae7", borderRadius: 2 }}>
      <Stack sx={{ flexDirection: "row", justifyContent: "space-between", alignItems: "flex-start", gap: 2 }}>
        <Box>
          <Typography variant="subtitle2" sx={{ fontWeight: 650, color: "#354238" }}>
            {title}
          </Typography>
          {subtitle && <Typography variant="caption" color="text.secondary">{subtitle}</Typography>}
        </Box>
        {action}
      </Stack>
      {children}
    </Paper>
  );
}

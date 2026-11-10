import { Avatar, Box, Divider, Stack, Typography } from "@mui/material";
import { Navigation } from "./navigation";

export function Sidebar({
  activePage,
  onNavigate,
}: {
  activePage: string;
  onNavigate: (page: string) => void;
}) {
  return (
    <Box
      component="aside"
      sx={{
        width: 220,
        flexShrink: 0,
        minHeight: "100vh",
        p: 2,
        bgcolor: "#fff",
        borderRight: "1px solid #e5eae7",
        display: { xs: "none", sm: "flex" },
        flexDirection: "column",
      }}
    >
      <Stack sx={{ flexDirection: "row", alignItems: "center", gap: 1, px: 1, py: 1.5, mb: 3 }}>
        <Avatar
          variant="rounded"
          sx={{ width: 30, height: 30, bgcolor: "#237247", fontSize: 13, fontWeight: 700 }}
        >
          S
        </Avatar>
        <Typography sx={{ fontWeight: 700, fontSize: 15, color: "#28372d" }}>
          StockTrack
        </Typography>
      </Stack>

      <Typography
        variant="overline"
        sx={{ px: 1, color: "#96a199", fontSize: 10, letterSpacing: 1 }}
      >
        WORKSPACE
      </Typography>
      <Typography variant="caption" sx={{ px: 1, mb: 2, color: "#7f8c83" }}>
        UK Fruit Ltd
      </Typography>
      <Divider sx={{ mb: 2 }} />

      <Navigation activePage={activePage} onNavigate={onNavigate} />
    </Box>
  );
}
